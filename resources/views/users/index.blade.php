@extends('layouts.mantis')

@section('header')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">User Management</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('daily-reports.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">Users</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>User List</h5>
                    {{-- Tombol Add dihapus, diganti dengan Info Badge --}}
                    <span class="badge bg-light-info text-info border">
                        <i class="ti ti-info-circle me-1"></i> User baru otomatis terdaftar via SSO
                    </span>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Restaurant</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('template/dist/assets/images/user/avatar-2.jpg') }}"
                                                    alt="user" class="wid-30 rounded-circle me-2">
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            {{-- Ambil Role Lagsung via Spatie (Tanpa Bridge) --}}
                                            @if ($user->roles->isNotEmpty())
                                                @foreach ($user->roles as $role)
                                                    <span class="badge bg-light-primary text-primary border border-primary">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- Ambil Restoran Langsung via Relasi --}}
                                            @if ($user->hasRole('Super Admin'))
                                                <span class="badge bg-dark">Global Admin</span>
                                            @elseif($user->restaurants->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach ($user->restaurants as $rest)
                                                        <span class="badge bg-light-secondary text-dark border">
                                                            {{ $rest->code }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-danger small fst-italic">No Access</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="btn btn-icon btn-link-warning btn-sm">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                            @if(Auth::id() != $user->id)
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                    class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-link-danger btn-sm"
                                                        onclick="return confirm('Cabut akses user {{ $user->name }}?')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $users->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection