@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 text-primary">Kode Booking: {{ $peminjaman->kode_booking }}</h2>
            <p class="mb-0 text-muted">Detail lengkap data peminjaman</p>
        </div>
        <a href="{{ route('pages.peminjaman.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- KOLOM KIRI: INFO & DOKUMEN --}}
        <div class="col-md-8">
            <div class="bg-light rounded p-4 mb-4">
                <h5 class="border-bottom pb-2 mb-4"><i class="fas fa-info-circle me-2"></i>Informasi Peminjaman</h5>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Status Pengajuan</div>
                    <div class="col-sm-8">
                        @php
                            $badges = [
                                'pending' => 'bg-warning text-dark',
                                'disetujui' => 'bg-info text-white',
                                'selesai' => 'bg-success',
                                'ditolak' => 'bg-danger',
                                'dibatalkan' => 'bg-secondary'
                            ];
                        @endphp
                        <span class="badge {{ $badges[$peminjaman->status] }} fs-6 px-3">{{ ucfirst($peminjaman->status) }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Fasilitas</div>
                    <div class="col-sm-8 fw-bold">{{ $peminjaman->fasilitas->nama }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Nama Peminjam</div>
                    <div class="col-sm-8">{{ $peminjaman->warga->nama }} ({{ $peminjaman->warga->no_ktp }})</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Tanggal Sewa</div>
                    <div class="col-sm-8">
                        {{ $peminjaman->tanggal_mulai->format('d F Y') }} 
                        <span class="text-muted mx-2">s/d</span> 
                        {{ $peminjaman->tanggal_selesai->format('d F Y') }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Tujuan</div>
                    <div class="col-sm-8">{{ $peminjaman->tujuan }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Total Biaya</div>
                    <div class="col-sm-8 fw-bold fs-5 text-success">Rp {{ number_format($peminjaman->total_biaya, 0, ',', '.') }}</div>
                </div>
                
                {{-- STATUS PEMBAYARAN --}}
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Status Pembayaran</div>
                    <div class="col-sm-8">
                        @if($peminjaman->pembayaran)
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> LUNAS</span>
                            <small class="text-muted d-block mt-1">
                                Dibayar tgl: {{ $peminjaman->pembayaran->tgl_bayar->format('d/m/Y') }} 
                                ({{ $peminjaman->pembayaran->metode }})
                            </small>
                        @elseif($peminjaman->total_biaya == 0)
                            <span class="badge bg-info">GRATIS</span>
                        @else
                            <span class="badge bg-danger">BELUM LUNAS</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-light rounded p-4">
                <h5 class="border-bottom pb-2 mb-4"><i class="fas fa-receipt me-2"></i>Bukti Pembayaran / Dokumen</h5>
                
                <div class="row g-3">
                    @forelse ($peminjaman->media as $media)
                        <div class="col-md-4 col-6">
                            <div class="card h-100 border-0 shadow-sm">
                                
                                {{-- LOGIKA PROXY URL: Pastikan file_name adalah path lengkap (bukti_bayar/xyz.jpg) --}}
                                @php
                                    $filePath = $media->file_name; 
                                    // Memecah path: Ambil 'bukti_bayar' dan 'namafile.png'
                                    $parts = explode('/', $filePath, 2);
                                    $folder = $parts[0] ?? 'bukti_bayar'; 
                                    $filename = $parts[1] ?? basename($filePath); 

                                    // Membuat URL menggunakan rute proxy
                                    $proxyUrl = route('storage.proxy', ['folder' => $folder, 'filename' => $filename]);
                                @endphp

                                <a href="{{ $proxyUrl }}" target="_blank" class="text-decoration-none">
                                    {{-- Cek apakah file adalah image (Menggunakan mime_type) --}}
                                    @if(Str::startsWith($media->mime_type, 'image/'))
                                        <img src="{{ $proxyUrl }}" class="card-img-top rounded-top" 
                                             style="height: 150px; object-fit: cover; border-radius: 10px 10px 0 0;" alt="Bukti Pembayaran">
                                    @else
                                        {{-- Jika PDF/Doc --}}
                                        <div class="d-flex flex-column align-items-center justify-content-center bg-secondary text-white p-3 rounded-top" style="height: 150px;">
                                            <i class="fas fa-file-alt fa-3x mb-2"></i>
                                            <small class="text-white-50">{{ strtoupper(pathinfo($filePath, PATHINFO_EXTENSION)) }} Document</small>
                                        </div>
                                    @endif
                                </a>
                                <div class="card-body p-2 text-center">
                                    <small class="d-block text-muted mb-1">{{ basename($filePath) }}</small>
                                    {{-- Link Unduh juga menggunakan proxy --}}
                                    <a href="{{ $proxyUrl }}" class="btn btn-sm btn-outline-primary" download="{{ basename($filePath) }}">
                                        <i class="fas fa-download"></i> Unduh
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-3">
                            <i class="fas fa-file-invoice fa-2x mb-2 d-block"></i>
                            Tidak ada dokumen diupload.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: AKSI --}}
        <div class="col-md-4">
            <div class="bg-light rounded p-4 sticky-top" style="top: 100px; z-index: 10;">
                <h5 class="mb-3 text-primary fw-bold">Aksi Admin</h5>
                
                {{-- TOMBOL SHORTCUT KE PEMBAYARAN --}}
                @if(!$peminjaman->pembayaran && $peminjaman->total_biaya > 0 && !in_array($peminjaman->status, ['ditolak', 'dibatalkan']))
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i> Transaksi ini belum dibayar lunas.
                    </div>
                    <a href="{{ route('pages.pembayaran.create', ['pinjam_id' => $peminjaman->pinjam_id]) }}" class="btn btn-warning w-100 mb-3 text-dark fw-bold">
                        <i class="fas fa-cash-register me-2"></i>Catat Pembayaran
                    </a>
                    <hr>
                @endif

                {{-- Form Update Status --}}
                <form action="{{ route('pages.peminjaman.update-status', $peminjaman->pinjam_id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="d-grid gap-2 mb-3">
                        @if($peminjaman->status == 'pending')
                            <button name="status" value="disetujui" class="btn btn-success text-white">
                                <i class="fas fa-check me-2"></i>Setujui Pengajuan
                            </button>
                            <button name="status" value="ditolak" class="btn btn-danger text-white">
                                <i class="fas fa-times me-2"></i>Tolak Pengajuan
                            </button>
                        @elseif($peminjaman->status == 'disetujui')
                            <button name="status" value="selesai" class="btn btn-primary text-white">
                                <i class="fas fa-flag-checkered me-2"></i>Tandai Selesai
                            </button>
                            <button name="status" value="dibatalkan" class="btn btn-secondary text-white">
                                <i class="fas fa-ban me-2"></i>Batalkan
                            </button>
                        @else
                            <div class="alert alert-secondary text-center small mb-0">
                                Transaksi sudah <b>{{ strtoupper($peminjaman->status) }}</b>.
                            </div>
                        @endif
                    </div>
                </form>

                <hr>

                {{-- Hapus Permanen --}}
                <form action="{{ route('pages.peminjaman.destroy', $peminjaman->pinjam_id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100" 
                            onclick="return confirm('Yakin ingin menghapus data ini selamanya?')">
                        <i class="fas fa-trash-alt me-2"></i>Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection