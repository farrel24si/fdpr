<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\FasilitasUmumController;
use Illuminate\Support\Facades\Response; // Pastikan ini ada
use Illuminate\Support\Facades\Storage; // Pastikan ini ada
use Illuminate\Support\Facades\Auth; // Tambahkan ini (walaupun sudah di CheckIsLogin)
use App\Http\Controllers\StorageController; // Buat controller baru


/*
|--------------------------------------------------------------------------
| 1. ROUTE UTILITY (PROXY STORAGE - AKSES LUAR MIDDLEWARE)
|--------------------------------------------------------------------------
| Dipindahkan ke sini agar tidak terhalang oleh masalah session/middleware
| saat browser me-load aset. Perlindungan Auth::check() dimasukkan secara inline.
*/
// Versi 1: Simple Proxy (tanpa controller)
Route::get('/storage-proxy/{folder}/{filename}', function ($folder, $filename) {
    // 1. Cek auth terlebih dahulu
    if (!Auth::check()) {
        abort(403, 'Silakan login terlebih dahulu');
    }
    
    // 2. Validasi folder yang diizinkan
    $allowedFolders = ['bukti_bayar', 'media', 'profiles'];
    if (!in_array($folder, $allowedFolders)) {
        abort(403, 'Folder tidak diizinkan');
    }
    
    // 3. Path lengkap file
    $path = $folder . '/' . $filename;
    
    // 4. Cek apakah file ada di storage public
    if (!Storage::disk('public')->exists($path)) {
        abort(404, 'File tidak ditemukan: ' . $path);
    }
    
    // 5. Dapatkan full path fisik
    $fullPath = Storage::disk('public')->path($path);
    
    // 6. Return file response
    return response()->file($fullPath);
})->name('storage.proxy'); // NAMA ROUTE: 'storage.proxy'

/*
|--------------------------------------------------------------------------
| 2. ROUTE PUBLIC / AUTH
|--------------------------------------------------------------------------
*/
Route::prefix('pages/auth')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('pages.auth.index');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('pages.auth.register.form');
    Route::post('/login', [AuthController::class, 'login'])->name('pages.auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('pages.auth.register');
    Route::post('/logout', [AuthController::class, 'logout'])->name('pages.auth.logout');
});

/*
|--------------------------------------------------------------------------
| 3. ROUTE PRIVATE (LOGIN REQUIRED)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['checkislogin']], function () {

    // --- A. AKSES SEMUA ROLE (User, Admin, Super Admin) ---
    Route::get('/', function () { return redirect()->route('dashboard'); });
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::prefix('pages/profile')->group(function () {
        Route::get('/show', [ProfileController::class, 'show'])->name('pages.profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('pages.profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('pages.profile.update');
        Route::delete('/destroy', [ProfileController::class, 'destroy'])->name('pages.profile.destroy');
    });

    // Developer Page
    Route::get('pages/developer', [DeveloperController::class, 'index'])->name('pages.developer.index');


    // --- B. KHUSUS SUPER ADMIN ---
    Route::group(['middleware' => ['checkrole:Super Admin']], function () {
        Route::resource('pages/user', UserController::class)->names([
            'index' => 'pages.user.index', 'create' => 'pages.user.create', 'store' => 'pages.user.store',
            'show' => 'pages.user.show', 'edit' => 'pages.user.edit', 'update' => 'pages.user.update', 'destroy' => 'pages.user.destroy',
        ]);
    });


    // --- C. ADMIN & SUPER ADMIN (User Biasa Dilarang Masuk) ---
    Route::group(['middleware' => ['checkrole:Super Admin,Admin']], function () {
        
        Route::resource('pages/warga', WargaController::class)->names([
            'index' => 'pages.warga.index', 'create' => 'pages.warga.create', 'store' => 'pages.warga.store',
            'show' => 'pages.warga.show', 'edit' => 'pages.warga.edit', 'update' => 'pages.warga.update', 'destroy' => 'pages.warga.destroy',
        ]);

        Route::resource('pages/fasilitas', FasilitasUmumController::class)->names([
            'index' => 'pages.fasilitas.index', 'create' => 'pages.fasilitas.create', 'store' => 'pages.fasilitas.store',
            'show' => 'pages.fasilitas.show', 'edit' => 'pages.fasilitas.edit', 'update' => 'pages.fasilitas.update', 'destroy' => 'pages.fasilitas.destroy',
        ]);

        Route::resource('pages/petugas', PetugasController::class)->names([
            'index' => 'pages.petugas.index', 'create' => 'pages.petugas.create', 'store' => 'pages.petugas.store',
            'edit' => 'pages.petugas.edit', 'update' => 'pages.petugas.update', 'destroy' => 'pages.petugas.destroy',
        ]);

        Route::resource('pages/peminjaman', PeminjamanController::class)->names([
            'index' => 'pages.peminjaman.index', 'create' => 'pages.peminjaman.create', 'store' => 'pages.peminjaman.store',
            'show' => 'pages.peminjaman.show', 'edit' => 'pages.peminjaman.edit', 'update' => 'pages.peminjaman.update', 'destroy' => 'pages.peminjaman.destroy',
        ]);
        Route::patch('pages/peminjaman/{id}/status', [PeminjamanController::class, 'updateStatus'])->name('pages.peminjaman.update-status');

        Route::resource('pages/pembayaran', PembayaranController::class)->names([
            'index' => 'pages.pembayaran.index', 'create' => 'pages.pembayaran.create', 'store' => 'pages.pembayaran.store',
            'edit' => 'pages.pembayaran.edit', 'update' => 'pages.pembayaran.update', 'destroy' => 'pages.pembayaran.destroy'
        ]);
    });
});