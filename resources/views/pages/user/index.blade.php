@extends('layouts.app')

@section('title', 'Data User')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Data User</h2>
            <p class="mb-0">List data seluruh user sistem</p>
        </div>
        <div>
            <a href="{{ route('pages.user.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Tambah User
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="bg-light rounded p-4">
        <form method="GET" action="{{ route('pages.user.index') }}" class="mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                               value="{{ request('search') }}" placeholder="Cari nama atau email...">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row g-2 justify-content-end">
                        <div class="col-md-3">
                            <select name="perPage" class="form-select" onchange="this.form.submit()">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('pages.user.index') }}" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="mb-3 text-muted">
            Menampilkan {{ $dataUser->firstItem() ?? 0 }} - {{ $dataUser->lastItem() ?? 0 }} dari {{ $dataUser->total() }} data
        </div>

        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-dark bg-white">
                        <th scope="col" width="5%">No</th>
                        <th scope="col" width="10%" class="text-center">Foto</th> {{-- KOLOM BARU --}}
                        <th scope="col">Nama</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataUser as $item)
                    <tr>
                        <td>{{ $loop->iteration + (($dataUser->currentPage() - 1) * $dataUser->perPage()) }}</td>
                        
                        {{-- MENAMPILKAN FOTO USER --}}
                        <td class="text-center">
                            <img src="{{ $item->getProfilePictureUrl() }}" 
                                 alt="Foto" 
                                 class="rounded-circle shadow-sm border"
                                 style="width: 40px; height: 40px; object-fit: cover;">
                        </td>

                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>
                            @if($item->role == 'Super Admin')
                                <span class="badge bg-danger">{{ $item->role }}</span>
                            @elseif($item->role == 'Admin')
                                <span class="badge bg-warning text-dark">{{ $item->role }}</span>
                            @else
                                <span class="badge bg-info text-dark">{{ $item->role }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('pages.user.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pages.user.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- Colspan jadi 6 karena tambah 1 kolom foto --}}
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                            Tidak ada data user ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $dataUser->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection