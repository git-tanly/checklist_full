@extends('layouts.mantis')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5>Grant Access to User</h5>
                    <small>Tambahkan user yang SUDAH ADA di Portal ke aplikasi Checklist</small>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            {{-- Email adalah Kunci Pencarian --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">User Email (Registered in Portal)</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                    placeholder="example@tanly.id" required>
                                <small class="text-muted">Masukkan email user yang sudah terdaftar di SSO Portal.</small>
                            </div>

                            {{-- Role --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Role Access</label>
                                <select name="role" class="form-select" required>
                                    <option value="" selected disabled>-- Select Role --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}"
                                            {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Restaurants --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Assigned Restaurants</label>
                                <div class="card border bg-light">
                                    <div class="card-body p-3">
                                        <div class="row">
                                            @foreach ($restaurants as $rest)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="restaurants[]"
                                                            value="{{ $rest->id }}" id="rest_{{ $rest->id }}">
                                                        <label class="form-check-label" for="rest_{{ $rest->id }}">
                                                            {{ $rest->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">Grant Access</button>
                            <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
