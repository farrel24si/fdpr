<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Tambahkan ini jika butuh akses Auth::user()

class UserController extends Controller
{
    public function index(Request $request)
    {
     

        $search  = $request->get('search');
        $perPage = $request->get('perPage', 10);

        $dataUser = User::when($search, function($query) use ($search) {
                return $query->search($search);
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->all());

        return view('pages.user.index', compact('dataUser', 'search', 'perPage'));
    }

    public function create()
    {
        // PEMBERSIHAN: Hapus if (!session(...))
        return view('pages.user.create');
    }

    public function store(Request $request)
    {
        // PEMBERSIHAN: Hapus if (!session(...))

        $request->validate([
            'name'     => 'required|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:Super Admin,Admin,User' // Tambahkan validasi role biar aman
        ]);

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role, // Simpan role yang dipilih di form
        ];

        User::create($data);

        return redirect()->route('pages.user.index')->with('success', 'Penambahan Data Berhasil!');
    }

    public function edit(string $id)
    {
        // PEMBERSIHAN: Hapus if (!session(...))

        $data['dataUser'] = User::findOrFail($id);
        return view('pages.user.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        // PEMBERSIHAN: Hapus if (!session(...))

        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|max:100',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'role'     => 'required|in:Super Admin,Admin,User' // Validasi role
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role, // Update role
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('pages.user.index')->with('success', 'Perubahan Data Berhasil!');
    }

    public function destroy(string $id)
    {
        // PEMBERSIHAN: Hapus if (!session(...))

        $user = User::findOrFail($id);

        // Ganti session('admin_email') dengan Auth::user()->email
        if ($user->id === Auth::id()) {
            return redirect()->route('pages.user.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('pages.user.index')->with('success', 'Data berhasil dihapus');
    }
}
