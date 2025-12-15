@extends('layouts.app')

@section('title', 'Edit Petugas')

@section('content')
<div class="container pt-4 pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- HEADER NAVIGASI --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Edit Data Petugas</h4>
                    <p class="text-muted mb-0">Perbarui penugasan dan peran untuk petugas ID: {{ $petugas->petugas_id }}</p>
                </div>
                <a href="{{ route('pages.petugas.index') }}" class="btn btn-secondary rounded-pill px-4 fw-bold">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>

            {{-- ALERT MESSAGES --}}
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-light rounded p-4">
                
                <form action="{{ route('pages.petugas.update', $petugas->petugas_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    {{-- 1. PILIH FASILITAS --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Target Fasilitas</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building text-primary"></i></span>
                            <select class="form-select @error('fasilitas_id') is-invalid @enderror" name="fasilitas_id" required>
                                @foreach ($fasilitas as $f)
                                    <option value="{{ $f->fasilitas_id }}" 
                                        {{ old('fasilitas_id', $petugas->fasilitas_id) == $f->fasilitas_id ? 'selected' : '' }}>
                                        {{ $f->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fasilitas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- 2. PILIH WARGA (READONLY DENGAN GAYA SELECT) --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Warga (Petugas)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-friends text-success"></i></span>
                            {{-- Input ini DIBUAT READONLY karena ID Warga tidak boleh diubah saat editing petugas, harus buat baru --}}
                            <input type="text" class="form-control bg-white" value="{{ $petugas->warga->nama }}" readonly style="cursor: default;">
                            <span class="input-group-text bg-light text-muted" title="Tidak dapat diubah"><i class="fas fa-lock small"></i></span>
                            
                            {{-- Kirim ID warga lama (Hidden Field) --}}
                            <input type="hidden" name="warga_id" value="{{ $petugas->warga_id }}">
                        </div>
                        <div class="form-text small mt-2">Warga yang ditugaskan tidak dapat diubah. Untuk mengubah warga, hapus data ini dan buat baru.</div>
                    </div>

                    <hr class="my-4">

                    {{-- 3. PERAN / JABATAN (RADIO BUTTON) --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-3">Peran / Jabatan</label>
                        
                        <div class="row g-3">
                            @foreach(['Penanggung Jawab', 'Operasional', 'Keamanan', 'Kebersihan'] as $role)
                            <div class="col-md-6">
                                {{-- FIX KLIK: Menggunakan position-relative --}}
                                <div class="form-check position-relative p-3 border rounded bg-white">
                                    <input class="form-check-input @error('peran') is-invalid @enderror" 
                                           type="radio" name="peran" 
                                           id="role_{{ $loop->index }}" 
                                           value="{{ $role }}" 
                                           required 
                                           {{ old('peran', $petugas->peran) == $role ? 'checked' : '' }}>
                                    
                                    {{-- Menggunakan stretched-link agar area klik label menjadi satu kotak --}}
                                    <label class="form-check-label w-100 stretched-link fw-bold text-dark ms-2" 
                                           for="role_{{ $loop->index }}">
                                        {{ $role }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        {{-- Tampilkan error peran di sini --}}
                        @error('peran')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TOMBOL SIMPAN --}}
                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-warning text-white fw-bold py-2 rounded-pill">
                            <i class="fas fa-edit me-2"></i> Update Data Petugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection