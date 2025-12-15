@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container pt-4 pb-5">
    
    {{-- ALERT MESSAGES --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- PROFILE CARD WRAPPER --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                
                {{-- 1. COVER PHOTO (Background Gradient) --}}
                <div style="height: 200px; background: linear-gradient(120deg, #0f766e 0%, #0d6efd 100%); position: relative;">
                    <div class="position-absolute top-0 end-0 p-4">
                        {{-- PERBAIKAN TOMBOL KEMBALI: Menggunakan Background Putih Solid & Teks Primary agar terlihat jelas --}}
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm rounded-pill shadow-sm hover-scale fw-bold text-primary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="row g-0">
                        
                        {{-- 2. SIDEBAR KIRI (FOTO & ACTIONS) --}}
                        <div class="col-md-4 bg-light border-end">
                            <div class="d-flex flex-column align-items-center text-center p-4" style="margin-top: -80px;">
                                
                                {{-- FOTO PROFIL --}}
                                <div class="position-relative mb-3">
                                    <img src="{{ $user->getProfilePictureUrl() }}" 
                                         alt="Profile Picture" 
                                         class="rounded-circle shadow-lg border border-4 border-white bg-white"
                                         style="width: 160px; height: 160px; object-fit: cover;">
                                    
                                    {{-- Status Indicator --}}
                                    <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle" 
                                          style="width: 20px; height: 20px; transform: translate(-10px, -10px);"
                                          title="Online"></span>
                                </div>

                                <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                                
                                {{-- PERBAIKAN ROLE: Menggunakan warna solid (bg-primary) agar teks putih terbaca jelas --}}
                                <span class="badge bg-primary rounded-pill px-3 py-2 mb-4 shadow-sm">
                                    {{ $user->role ?? 'Pengguna' }}
                                </span>

                                {{-- ACTION BUTTONS --}}
                                <div class="d-grid gap-2 w-100 px-3">
                                    <a href="{{ route('pages.profile.edit') }}" class="btn btn-warning fw-bold text-white shadow-sm">
                                        <i class="fas fa-edit me-2"></i> Edit Profil
                                    </a>
                                    
                                    @if ($user->profile_picture)
                                        <form action="{{ route('pages.profile.destroy') }}" method="POST" class="d-grid">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm border-0"
                                                onclick="return confirm('Yakin ingin menghapus foto profil?')">
                                                <i class="fas fa-trash me-2"></i> Hapus Foto
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 3. KONTEN KANAN (DETAIL INFO) --}}
                        <div class="col-md-8 bg-white p-4 p-md-5">
                            <h5 class="fw-bold text-secondary text-uppercase mb-4 border-bottom pb-2">
                                <i class="fas fa-id-card me-2"></i> Informasi Akun
                            </h5>

                            <div class="row g-4">
                                {{-- Email --}}
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-3 rounded-circle me-3 text-primary">
                                            <i class="fas fa-envelope fa-lg"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Alamat Email</small>
                                            <span class="fs-5 text-dark">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tanggal Bergabung --}}
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-3 rounded-circle me-3 text-success">
                                            <i class="fas fa-calendar-check fa-lg"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Bergabung Sejak</small>
                                            <span class="fw-bold text-dark">{{ $user->created_at->format('d F Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Terakhir Update --}}
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-3 rounded-circle me-3 text-info">
                                            <i class="fas fa-history fa-lg"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Terakhir Diupdate</small>
                                            <span class="fw-bold text-dark">{{ $user->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.05); }
</style>
@endpush