<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('pages.auth.login-form');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('pages.auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek User & Password
        if ($user && Hash::check($request->password, $user->password)) {
            
            // Login Session
            Auth::login($user);

            return redirect()->route('dashboard')
                ->with('success', 'Selamat Datang ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah!',
        ])->withInput($request->only('email'));
    }

    public function register(Request $request)
    {
        // 1. Validasi Custom (No Numbers)
        Validator::extend('no_numbers', function ($attribute, $value, $parameters, $validator) {
            return ! preg_match('/[0-9]/', $value);
        });

        $validator = Validator::make($request->all(), [
            'nama'             => 'required|string|no_numbers',
            'email'            => 'required|email|unique:users,email',
            'password'         => [
                'required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/',
            ],
            'confirm_password' => 'required|string|same:password',
        ], [
            'nama.no_numbers' => 'Nama tidak boleh mengandung angka',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'User', 
            ]);

            return redirect()->route('pages.auth.index')
                ->with('success', 'Registrasi berhasil! Silakan Login.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['registration_error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pages.auth.index')
            ->with('success', 'Anda telah logout!');
    }
}