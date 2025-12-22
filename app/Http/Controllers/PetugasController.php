<?php

namespace App\Http\Controllers;

use App\Models\PetugasFasilitas;
use App\Models\FasilitasUmum;
use App\Models\Warga;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    // 1. INDEX (Update: Tambah Search, Filter, dan Pagination Dinamis)
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10); 
        $search  = $request->get('search');
        $fasilitasId = $request->get('fasilitas_id');
        $peran   = $request->get('peran');
        $query = PetugasFasilitas::with(['fasilitas', 'warga']);
        if ($search) {
            $query->whereHas('warga', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }
        if ($fasilitasId) {
            $query->where('fasilitas_id', $fasilitasId);
        }
        if ($peran) {
            $query->where('peran', $peran);
        }
        $petugas = $query->latest()->paginate($perPage)->withQueryString();
        $allFasilitas = FasilitasUmum::all();

        return view('pages.petugas.index', compact('petugas', 'allFasilitas', 'perPage'));
    }

    public function create()
    {
        $fasilitas = FasilitasUmum::all();
        $warga = Warga::all();
        return view('pages.petugas.create', compact('fasilitas', 'warga'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fasilitas_id' => 'required|exists:fasilitas_umum,fasilitas_id',
            'warga_id'     => 'required|exists:warga,warga_id',
            'peran'        => 'required|in:Penanggung Jawab,Operasional,Keamanan,Kebersihan',
        ]);

        PetugasFasilitas::create($validated);

        return redirect()->route('pages.petugas.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $petugas = PetugasFasilitas::findOrFail($id);
        $fasilitas = FasilitasUmum::all();
        $warga = Warga::all();

        return view('pages.petugas.edit', compact('petugas', 'fasilitas', 'warga'));
    }

    public function update(Request $request, $id)
    {
        $petugas = PetugasFasilitas::findOrFail($id);

        $validated = $request->validate([
            'fasilitas_id' => 'required|exists:fasilitas_umum,fasilitas_id',
            'warga_id'     => 'required|exists:warga,warga_id',
            'peran'        => 'required|in:Penanggung Jawab,Operasional,Keamanan,Kebersihan',
        ]);

        $petugas->update($validated);

        return redirect()->route('pages.petugas.index')->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        PetugasFasilitas::findOrFail($id)->delete();
        return redirect()->route('pages.petugas.index')->with('success', 'Petugas berhasil dihapus.');
    }
}