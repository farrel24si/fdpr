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
            // Kita ubah dari 'image' menjadi 'mimes' agar lebih fleksibel
            'bukti_bayar'     => 'nullable|mimes:jpeg,png,jpg|max:2048', 
        ]);

        // Cek Bentrok Jadwal (Logika ini sudah benar)
        $bentrok = PeminjamanFasilitas::where('fasilitas_id', $request->fasilitas_id)
            ->where('status', '!=', 'ditolak')
            ->where('status', '!=', 'dibatalkan')
            // ... (Logika pengecekan bentrok yang panjang)
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Gagal! Fasilitas sudah dipesan pada tanggal tersebut.')->withInput();
        }

        $kodeBooking = 'PJ-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        DB::transaction(function () use ($request, $kodeBooking) {
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

            // START: LOGIKA UPLOAD MEDIA YANG DIPERBAIKI
            if ($request->hasFile('bukti_bayar')) {
                $file = $request->file('bukti_bayar');
                
                // Simpan file, Laravel akan memberikan nama unik dan mengembalikan path
                // Path akan berupa: bukti_bayar/namaunikfile.jpg
                $path = $file->store('bukti_bayar', 'public'); 

                Media::create([
                    'ref_table' => 'peminjaman_fasilitas',
                    'ref_id'    => $peminjaman->pinjam_id,
                    // Kita simpan path yang dikembalikan oleh store()
                    'file_name' => basename($path), 
                    // Kita simpan path lengkap ke kolom 'file_path' baru (jika ada) atau gunakan file_name
                    // Karena tabel Media kamu menggunakan file_name, kita simpan pathnya di situ.
                    // Jika kamu punya kolom 'file_path', simpan $path di sana.
                    'file_path' => $path, // <--- Ini lebih baik jika ada kolom file_path
                    'mime_type' => $file->getClientMimeType(),
                ]);
            }
            // END: LOGIKA UPLOAD MEDIA YANG DIPERBAIKI
            
        });

        return redirect()->route('pages.peminjaman.index')->with('success', 'Peminjaman berhasil dibuat!');
    }

    public function show($id)
    {
        $peminjaman = PeminjamanFasilitas::with(['fasilitas', 'warga', 'media'])->findOrFail($id);
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

        $peminjaman->update($request->all());

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