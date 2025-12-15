<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    // Wajib untuk ambil data user login
use Illuminate\Support\Facades\Storage; // Wajib untuk kelola file
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil user yang sedang login
     */
    public function show()
    {
        $user = Auth::user(); // Ambil data user yang sedang login saat ini
        return view('pages.profile.show', compact('user'));
    }

    /**
     * Menampilkan form edit profil
     */
    public function edit()
    {
        $user = Auth::user();
        return view('pages.profile.edit', compact('user'));
    }

    /**
     * Update profil (Nama, Email, Foto)
     */
    public function update(Request $request)
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Kita cari ulang instance User berdasarkan ID agar aman saat save()
        $userData = User::findOrFail($user->id);

        // 1. Validasi Input
        $request->validate([
            'name'            => 'required|string|max:255',
            // Email wajib unik di tabel users, tapi abaikan ID user yang sedang login
            'email'           => 'required|email|max:255|unique:users,email,' . $userData->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. Update Data Teks (Nama & Email)
        $userData->name = $request->name;
        $userData->email = $request->email;

        // 3. Cek apakah ada file foto baru yang diupload
        if ($request->hasFile('profile_picture')) {
            
            // Hapus foto lama jika ada (dan file fisiknya ada)
            if ($userData->profile_picture && Storage::disk('public')->exists($userData->profile_picture)) {
                Storage::disk('public')->delete($userData->profile_picture);
            }

            // Simpan foto baru ke folder 'profile_pictures' di disk 'public'
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            
            // Simpan path baru ke database
            $userData->profile_picture = $path;
        }

        // 4. Simpan Perubahan ke Database
        $userData->save();

        return redirect()->route('pages.profile.show')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Hapus foto profil
     */
    public function destroy()
    {
        $user = Auth::user();
        $userData = User::findOrFail($user->id);

        // Cek jika ada foto, hapus filenya
        if ($userData->profile_picture && Storage::disk('public')->exists($userData->profile_picture)) {
            Storage::disk('public')->delete($userData->profile_picture);

            // Set kolom di database jadi NULL
            $userData->profile_picture = null;
            $userData->save();
        }

        return redirect()->route('pages.profile.show')->with('success', 'Foto profil berhasil dihapus!');
    }
}