@extends('layouts.app')

@section('title', 'Edit Peminjaman')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 text-primary fw-bold">Edit Booking</h2>
            <p class="mb-0 text-muted">Ubah data peminjaman atau perbarui status.</p>
        </div>
        <a href="{{ route('pages.peminjaman.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="bg-light rounded p-4 shadow-sm">
        {{-- TAMBAHKAN enctype="multipart/form-data" --}}
        <form action="{{ route('pages.peminjaman.update', $peminjaman->pinjam_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- KOLOM KIRI (Informasi Peminjam, Fasilitas, Tujuan) --}}
                <div class="col-md-6">
                    <h5 class="mb-3 text-info"><i class="fas fa-user-tag me-2"></i>Informasi Peminjam</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Peminjam <span class="text-danger">*</span></label>
                        <select class="form-select @error('warga_id') is-invalid @enderror" name="warga_id" required>
                            <option value="">-- Pilih Warga --</option>
                            @foreach ($warga as $w)
                                <option value="{{ $w->warga_id }}" 
                                    {{ old('warga_id', $peminjaman->warga_id) == $w->warga_id ? 'selected' : '' }}>
                                    {{ $w->nama }} ({{ $w->no_ktp }})
                                </option>
                            @endforeach
                        </select>
                        @error('warga_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fasilitas yang Disewa <span class="text-danger">*</span></label>
                        <select class="form-select @error('fasilitas_id') is-invalid @enderror" name="fasilitas_id" required>
                            <option value="">-- Pilih Fasilitas --</option>
                            @foreach ($fasilitas as $f)
                                <option value="{{ $f->fasilitas_id }}" 
                                    {{ old('fasilitas_id', $peminjaman->fasilitas_id) == $f->fasilitas_id ? 'selected' : '' }}>
                                    {{ $f->nama }} (Kapasitas: {{ $f->kapasitas }} org)
                                </option>
                            @endforeach
                        </select>
                        @error('fasilitas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tujuan Penggunaan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('tujuan') is-invalid @enderror" name="tujuan" rows="3" required>{{ old('tujuan', $peminjaman->tujuan) }}</textarea>
                        @error('tujuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- KOLOM KANAN (Waktu, Biaya, Status) --}}
                <div class="col-md-6">
                    <h5 class="mb-3 text-info"><i class="fas fa-clock me-2"></i>Waktu & Status</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mulai <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                name="tanggal_mulai" 
                                value="{{ old('tanggal_mulai', \Carbon\Carbon::parse($peminjaman->tanggal_mulai)->format('Y-m-d\TH:i')) }}" required>
                            @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Selesai <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                name="tanggal_selesai" 
                                value="{{ old('tanggal_selesai', \Carbon\Carbon::parse($peminjaman->tanggal_selesai)->format('Y-m-d\TH:i')) }}" required>
                            @error('tanggal_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Total Biaya (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control @error('total_biaya') is-invalid @enderror" 
                                name="total_biaya" value="{{ old('total_biaya', $peminjaman->total_biaya) }}" min="0" required>
                        </div>
                        @error('total_biaya') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Booking <span class="text-danger">*</span></label>
                        <select class="form-select border-3 border-warning @error('status') is-invalid @enderror" name="status" required>
                            @php
                                $statuses = ['pending', 'disetujui', 'ditolak', 'selesai', 'dibatalkan'];
                            @endphp
                            @foreach($statuses as $st)
                                <option value="{{ $st }}" {{ old('status', $peminjaman->status) == $st ? 'selected' : '' }}>
                                    {{ ucfirst($st) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Ubah status ini untuk menyetujui atau menolak booking.</div>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- BAGIAN BUKTI PEMBAYARAN --}}
            <div class="row">
                <div class="col-12">
                    <h5 class="mb-3 text-info"><i class="fas fa-receipt me-2"></i>Bukti Pembayaran</h5>
                    
                    {{-- INPUT UPLOAD FILE BARU --}}
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <i class="fas fa-upload me-2"></i>Upload Bukti Bayar Baru
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">File Bukti Bayar</label>
                                <input class="form-control @error('bukti_bayar') is-invalid @enderror" 
                                    type="file" name="bukti_bayar" accept="image/jpeg,image/png,image/jpg,application/pdf">
                                <div class="form-text">Format: JPG, PNG, PDF (Max 2MB). File akan ditambahkan ke daftar.</div>
                                @error('bukti_bayar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- TAMPILKAN BUKTI BAYAR YANG SUDAH ADA --}}
                    @php
                        // Load media dengan eager loading
                        $peminjaman->load('media');
                    @endphp
                    
                    @if($peminjaman->media->count() > 0)
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <i class="fas fa-images me-2"></i>Bukti Bayar yang Sudah Ada ({{ $peminjaman->media->count() }} file)
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach($peminjaman->media as $media)
                                        <div class="col-md-3 col-6">
                                            <div class="card border h-100">
                                                @php
                                                    $imageUrl = asset('storage/bukti_bayar/' . $media->file_name);
                                                    $isImage = \Illuminate\Support\Str::startsWith($media->file_name, ['image/', 'jpg', 'jpeg', 'png', 'gif']) 
                                                                || \Illuminate\Support\Str::endsWith($media->file_name, ['.jpg', '.jpeg', '.png', '.gif']);
                                                    $isPDF = \Illuminate\Support\Str::endsWith($media->file_name, '.pdf');
                                                @endphp
                                                
                                                <a href="{{ $imageUrl }}" target="_blank" class="text-decoration-none">
                                                    @if($isImage)
                                                        <img src="{{ $imageUrl }}" 
                                                             class="card-img-top rounded-top" 
                                                             style="height: 120px; object-fit: cover;"
                                                             alt="Bukti Pembayaran"
                                                             onerror="this.onerror=null; this.src='https://via.placeholder.com/200x120?text=Image'">
                                                    @elseif($isPDF)
                                                        <div class="d-flex flex-column align-items-center justify-content-center bg-danger text-white p-3" 
                                                             style="height: 120px; border-radius: 5px 5px 0 0;">
                                                            <i class="fas fa-file-pdf fa-3x mb-2"></i>
                                                            <small>PDF Document</small>
                                                        </div>
                                                    @else
                                                        <div class="d-flex flex-column align-items-center justify-content-center bg-secondary text-white p-3" 
                                                             style="height: 120px; border-radius: 5px 5px 0 0;">
                                                            <i class="fas fa-file-alt fa-3x mb-2"></i>
                                                            <small>Document</small>
                                                        </div>
                                                    @endif
                                                </a>
                                                
                                                <div class="card-body p-2 text-center">
                                                    <small class="d-block text-muted mb-1 text-truncate">
                                                        {{ \Illuminate\Support\Str::limit($media->file_name, 15) }}
                                                    </small>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ $imageUrl }}" 
                                                           class="btn btn-outline-primary" 
                                                           target="_blank" 
                                                           title="Lihat">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ $imageUrl }}" 
                                                           class="btn btn-outline-success" 
                                                           download="{{ $media->file_name }}"
                                                           title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Belum ada bukti pembayaran yang diupload.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('pages.peminjaman.index') }}" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-warning text-white px-4 fw-bold">
                    <i class="fas fa-save me-2"></i>SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>
@endsection