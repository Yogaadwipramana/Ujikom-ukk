@extends('layouts.templates')

@section('title', 'User List')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<head>
    <style>
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .btn-group {
            display: flex;
            gap: 5px;
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ddd;
        }
    </style>
</head>

<div class="container mt-5">
    <h2 class="fw-bold mb-3">User</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">User</li>
            </ol>
        </nav>
        <div>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm px-4">+ Tambah User</a>
            <a href="{{ route('users.export') }}" class="btn btn-success btn-sm px-4">Export</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr class="text-muted">
                        <th>#</th>
                        <th>Email</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    {{-- @php
                        // Periksa apakah ada user dengan role "admin"
                        $hasAdmin = $users->contains(function ($user) {
                            return $user->role === 'admin';
                        });
                    @endphp --}}


                    @foreach ($users as $index => $user)
                        @if ($user->role !== 'admin') <!-- Hanya tampilkan jika role bukan admin -->
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->name }}</td>
                                <td><span class="badge bg-primary">{{ ucfirst($user->role) }}</span></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>

                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal{{ $user->id }}">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="deleteConfirmModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-danger">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <p>Apakah Anda yakin ingin menghapus user <strong>{{ $user->name }}</strong>?</p>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-center">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(session('deleted'))
    <div class="modal fade show" id="userDeletedModal" tabindex="-1" aria-labelledby="userDeletedModalLabel" aria-hidden="true" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Berhasil Dihapus</h5>
                    <button type="button" class="btn-close" onclick="closeDeletedModal()"></button>
                </div>
                <div class="modal-body text-center">
                    <p>{{ session('deleted') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closeDeletedModal() {
            document.getElementById('userDeletedModal').style.display = 'none';
        }
        setTimeout(closeDeletedModal, 2000);
    </script>
@endif
@endsection
