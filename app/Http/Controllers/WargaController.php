<?php
namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $search = $request->get('search');
    $filters = $request->only(['jenis_kelamin', 'agama', 'pekerjaan']);
    $perPage = $request->get('perPage', 10); // Default 10

    $dataWarga = Warga::when($search, function($query) use ($search) {
                return $query->search($search);
            })
            ->when($filters, function($query) use ($filters) {
                return $query->filter($filters);
            })
            ->orderBy('warga_id', 'desc')
            ->paginate($perPage)
            ->appends($request->all()); 

    return view('pages.warga.index', compact('dataWarga', 'filters', 'search', 'perPage'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.warga.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // VALIDATION RULES
        $validated = $request->validate([
            'no_ktp' => 'required|unique:warga|max:16',
            'nama' => 'required|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'pekerjaan' => 'required|max:50',
            'telp' => 'required|max:15',
            'email' => 'required|email|unique:warga',
        ], [
            'no_ktp.required' => 'No KTP wajib diisi',
            'no_ktp.unique' => 'No KTP tidak valid',
            'nama.required' => 'Nama wajib diisi',
            'email.unique' => 'Email sudah terdaftar',

        ]);

        // Jika validation passed, create data
        Warga::create($validated);

        return redirect()->route('pages.warga.index')
            ->with('success', 'Data warga berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['dataWarga'] = Warga::findOrFail($id);
        return view('pages.warga.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, string $id)
    {
        $warga = Warga::findOrFail($id);

        // VALIDATION RULES untuk update (ignore unique untuk data yang sama)
        $validated = $request->validate([
            'no_ktp' => 'required|max:16|unique:warga,no_ktp,' . $id . ',warga_id',
            'nama' => 'required|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'pekerjaan' => 'required|max:50',
            'telp' => 'required|max:15',
            'email' => 'required|email|unique:warga,email,' . $id . ',warga_id',
        ]);

        $warga->update($validated);

        return redirect()->route('pages.warga.index')
            ->with('success', 'Data warga berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warga = Warga::findOrFail($id);
        $warga->delete();

        return redirect()->route('pages.warga.index')->with('success', 'Data warga berhasil dihapus');
    }
}


