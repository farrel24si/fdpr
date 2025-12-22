@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container pt-4 pb-5">
    
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- HEADER NAVIGATION --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Edit Profil</h4>
                    <p class="text-muted mb-0">Perbarui foto dan informasi akun Anda.</p>
                </div>
                <a href="{{ route('pages.profile.show') }}" class="btn btn-light border shadow-sm rounded-pill px-4 fw-bold text-secondary hover-scale">
                    <i class="fas fa-times me-2"></i> Batal
                </a>
            </div>

            {{-- ERROR MESSAGES --}}
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                        <strong>Terjadi Kesalahan!</strong>
                    </div>
                    <ul class="mb-0 small text-danger">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('pages.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    
                    {{-- HEADER DEKORATIF --}}
                    <div style="height: 100px; background: linear-gradient(120deg, #0f766e 0%, #0d6efd 100%);"></div>

                    <div class="card-body p-0">
                        <div class="row g-0">
                            
                            {{-- KOLOM KIRI: UPLOAD FOTO --}}
                            <div class="col-md-4 bg-light border-end">
                                <div class="p-4 text-center" style="margin-top: -60px;">
                                    
                                    {{-- Preview Image --}}
                                    <div class="position-relative d-inline-block mb-3">
                                        <img id="image-preview" 
                                             src="{{ $user->getProfilePictureUrl() }}" 
                                             alt="Preview" 
                                             class="rounded-circle shadow-lg border border-4 border-white bg-white"
                                             style="width: 180px; height: 180px; object-fit: cover;">
                                        
                                        <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-2 shadow border border-light" 
                                             style="transform: translate(-10px, -10px);">
                                            <i class="fas fa-camera text-primary"></i>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-dark mb-3">Foto Profil</h6>

                                    {{-- Input File --}}
                                    <div class="mb-3">
                                        <label for="profile_picture" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                                            <i class="fas fa-upload me-2"></i> Pilih Foto Baru
                                        </label>
                                        <input type="file" class="d-none" 
                                               id="profile_picture" name="profile_picture" 
                                               accept="image/jpeg,image/png,image/jpg">
                                        <div class="form-text small mt-2">
                                            Maks: 2MB (JPG, PNG).
                                        </div>
                                    </div>

                                    {{-- Tombol Hapus Foto (Jika Ada) --}}
                                    @if ($user->profile_picture)
                                        <hr class="my-3 opacity-10">
                                        <button type="submit" form="delete-photo-form" 
                                                class="btn btn-link text-danger text-decoration-none btn-sm fw-bold"
                                                onclick="return confirm('Yakin ingin menghapus foto profil?')">
                                            <i class="fas fa-trash me-2"></i> Hapus Foto Saat Ini
                                        </button>
                                    @endif

                                </div>
                            </div>

                            {{-- KOLOM KANAN: FORM DATA --}}
                            {{-- KOLOM KANAN: FORM DATA --}}
                            <div class="col-md-8 bg-white p-4 p-md-5">
                                <h5 class="fw-bold text-secondary mb-4">
                                    <i class="fas fa-user-edit me-2"></i> Informasi Dasar
                                </h5>

                                {{-- NAMA (EDITABLE) --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                                        
                                        {{-- Tambahkan name="name" dan hapus readonly --}}
                                        <input type="text" 
                                               class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                               id="name" name="name"
                                               value="{{ old('name', $user->name) }}" 
                                               placeholder="Masukkan nama lengkap">
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- EMAIL (EDITABLE) --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Alamat Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                                        
                                        {{-- Tambahkan name="email" dan hapus readonly --}}
                                        <input type="email" 
                                               class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                               id="email" name="email"
                                               value="{{ old('email', $user->email) }}"
                                               placeholder="Masukkan alamat email">
                                    </div>
                                    <div class="form-text small"><i class="fas fa-info-circle me-1"></i> Pastikan email aktif.</div>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('pages.profile.show') }}" class="btn btn-light px-4 fw-bold text-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm hover-scale">
                                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Hidden Form untuk Hapus Foto --}}
            @if ($user->profile_picture)
                <form id="delete-photo-form" action="{{ route('pages.profile.destroy') }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            @endif

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Script Preview Image Real-time
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
