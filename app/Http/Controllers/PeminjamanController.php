<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanFasilitas;
use App\Models\FasilitasUmum;
use App\Models\Warga;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    // 1. INDEX (Update: Search + Filter + Pagination)
    public function index(Request $request)
    {
        // Ambil parameter filter dari URL
        $perPage     = $request->get('perPage', 10);
        $search      = $request->get('search');
        $status      = $request->get('status');
        $fasilitasId = $request->get('fasilitas_id');

        // Query Dasar dengan Eager Loading
        $query = PeminjamanFasilitas::with(['fasilitas', 'warga']);

        // Logika Pencarian (Kode Booking / Nama Peminjam / Nama Fasilitas)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_booking', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
                  ->orWhereHas('warga', function($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('fasilitas', function($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Status
        if ($status) {
            $query->where('status', $status);
        }

        // Filter Fasilitas
        if ($fasilitasId) {
            $query->where('fasilitas_id', $fasilitasId);
        }

        // Eksekusi Pagination
        $peminjaman = $query->latest('created_at')
                            ->paginate($perPage)
                            ->withQueryString();

        // Data untuk Dropdown Filter
        $allFasilitas = FasilitasUmum::all();

        return view('pages.peminjaman.index', compact('peminjaman', 'allFasilitas', 'perPage'));
    }

    // Form Tambah Peminjaman
    public function create()
    {
        $warga = Warga::all();
        $fasilitas = FasilitasUmum::all();
        return view('pages.peminjaman.create', compact('warga', 'fasilitas'));
    }

    // Proses Simpan Data
    public function store(Request $request)
    {
        $request->validate([
            'warga_id'        => 'required|exists:warga,warga_id',
            'fasilitas_id'    => 'required|exists:fasilitas_umum,fasilitas_id',
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tujuan'          => 'required|string',
            'total_biaya'     => 'required|numeric|min:0',
            'bukti_bayar'     => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048', 
        ]);

        // Cek bentrok jadwal
        $bentrok = PeminjamanFasilitas::where('fasilitas_id', $request->fasilitas_id)
            ->where('status', '!=', 'ditolak')
            ->where('status', '!=', 'dibatalkan')
            ->where(function ($query) use ($request) {
                $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
                      ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('tanggal_mulai', '<=', $request->tanggal_mulai)
                            ->where('tanggal_selesai', '>=', $request->tanggal_selesai);
                      });
            })
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Gagal! Fasilitas sudah dipesan pada tanggal tersebut.')->withInput();
        }

        $kodeBooking = 'PJ-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        DB::transaction(function () use ($request, $kodeBooking) {
            // 1. Buat peminjaman
            $peminjaman = PeminjamanFasilitas::create([
                'fasilitas_id'    => $request->fasilitas_id,
                'warga_id'        => $request->warga_id,
                'kode_booking'    => $kodeBooking,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'tujuan'          => $request->tujuan,
                'total_biaya'     => $request->total_biaya,
                'status'          => 'pending',
            ]);

            // 2. Upload bukti bayar jika ada
            if ($request->hasFile('bukti_bayar')) {
                $file = $request->file('bukti_bayar');
                
                // Generate nama file yang unik
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // Simpan ke storage
                $file->storeAs('bukti_bayar', $fileName, 'public');
                
                // Simpan ke database
                Media::create([
                    'ref_table'  => 'peminjaman_fasilitas',
                    'ref_id'     => $peminjaman->pinjam_id,
                    'file_name'  => $fileName, // Hanya nama file saja
                    'mime_type'  => $file->getClientMimeType(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('pages.peminjaman.index')->with('success', 'Peminjaman berhasil dibuat!');
    }

    public function show($id)
{
    $peminjaman = PeminjamanFasilitas::with([
        'fasilitas',
        'warga', 
        'media' // Pastikan relasi media ada di model PeminjamanFasilitas
    ])->findOrFail($id);
    
    return view('pages.peminjaman.show', compact('peminjaman'));
}

    public function edit($id)
    {
        $peminjaman = PeminjamanFasilitas::findOrFail($id);
        $warga = Warga::all();
        $fasilitas = FasilitasUmum::all();
        
        return view('pages.peminjaman.edit', compact('peminjaman', 'warga', 'fasilitas'));
    }

    public function update(Request $request, $id)
{
    $peminjaman = PeminjamanFasilitas::findOrFail($id);

    $request->validate([
        'warga_id'        => 'required|exists:warga,warga_id',
        'fasilitas_id'    => 'required|exists:fasilitas_umum,fasilitas_id',
        'tanggal_mulai'   => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        'tujuan'          => 'required|string',
        'total_biaya'     => 'required|numeric|min:0',
        'status'          => 'required|in:pending,disetujui,ditolak,selesai,dibatalkan',
        'bukti_bayar'     => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048', // TAMBAHKAN VALIDASI INI
    ]);

    // Cek Bentrok (Kecuali punya sendiri)
    $bentrok = PeminjamanFasilitas::where('fasilitas_id', $request->fasilitas_id)
        ->where('pinjam_id', '!=', $id)
        ->where('status', '!=', 'ditolak')
        ->where('status', '!=', 'dibatalkan')
        ->where(function ($query) use ($request) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
                  ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai]);
        })
        ->exists();

    if ($bentrok) {
        return back()->with('error', 'Tanggal bentrok dengan peminjaman lain!')->withInput();
    }

    // Update data peminjaman
    $peminjaman->update($request->except('bukti_bayar'));

    // Upload bukti bayar baru jika ada
    if ($request->hasFile('bukti_bayar')) {
        $file = $request->file('bukti_bayar');
        
        // Generate nama file yang unik
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Simpan ke storage
        $file->storeAs('bukti_bayar', $fileName, 'public');
        
        // Simpan ke database
        Media::create([
            'ref_table'  => 'peminjaman_fasilitas',
            'ref_id'     => $peminjaman->pinjam_id,
            'file_name'  => $fileName,
            'mime_type'  => $file->getClientMimeType(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return redirect()->route('pages.peminjaman.index')->with('success', 'Data peminjaman diperbarui.');
}

    public function updateStatus(Request $request, $id)
    {
        $peminjaman = PeminjamanFasilitas::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,disetujui,ditolak,selesai,dibatalkan']);
        $peminjaman->update(['status' => $request->status]);
        return back()->with('success', 'Status peminjaman diperbarui.');
    }

    public function destroy($id)
    {
        $peminjaman = PeminjamanFasilitas::findOrFail($id);
        $peminjaman->delete();
        return redirect()->route('pages.peminjaman.index')->with('success', 'Data peminjaman dihapus.');
    }
}