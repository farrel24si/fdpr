<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    // Wajib untuk ambil data user login
use Illuminate\Support\Facades\Storage; // Wajib untuk kelola file
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('pages.profile.show', compact('user'));
    }
    public function edit()
    {
        $user = Auth::user();
        return view('pages.profile.edit', compact('user'));
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $userData = User::findOrFail($user->id);
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email,' . $userData->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $userData->name = $request->name;
        $userData->email = $request->email;
        if ($request->hasFile('profile_picture')) {
            if ($userData->profile_picture && Storage::disk('public')->exists($userData->profile_picture)) {
                Storage::disk('public')->delete($userData->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $userData->profile_picture = $path;
        }
        $userData->save();
        return redirect()->route('pages.profile.show')
            ->with('success', 'Profil berhasil diperbarui!');
    }
    public function destroy()
    {
        $user = Auth::user();
        $userData = User::findOrFail($user->id);
        if ($userData->profile_picture && Storage::disk('public')->exists($userData->profile_picture)) {
            Storage::disk('public')->delete($userData->profile_picture);
            $userData->profile_picture = null;
            $userData->save();
        }

        return redirect()->route('pages.profile.show')->with('success', 'Foto profil berhasil dihapus!');
    }
}