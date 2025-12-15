<?php
// app/Http/Controllers/StorageController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StorageController extends Controller
{
    public function show($folder, $filename)
    {
        // 1. Cek authentication
        if (!Auth::check()) {
            abort(403, 'Silakan login untuk mengakses file ini');
        }
        
        // 2. Validasi folder
        $allowedFolders = ['bukti_bayar', 'media', 'profiles'];
        if (!in_array($folder, $allowedFolders)) {
            abort(403, 'Folder tidak diizinkan');
        }
        
        // 3. Cek file exists
        $path = $folder . '/' . $filename;
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File tidak ditemukan');
        }
        
        // 4. Get file
        $filePath = Storage::disk('public')->path($path);
        $mimeType = mime_content_type($filePath);
        
        // 5. Return response
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
    
    public function download($folder, $filename)
    {
        // Sama seperti show, tapi untuk download
        if (!Auth::check()) {
            abort(403);
        }
        
        $allowedFolders = ['bukti_bayar', 'media', 'profiles'];
        if (!in_array($folder, $allowedFolders)) {
            abort(403);
        }
        
        $path = $folder . '/' . $filename;
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }
        
        return Storage::disk('public')->download($path);
    }
}