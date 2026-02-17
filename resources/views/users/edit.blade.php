@extends('layouts.mantis')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5>Edit User Access</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Readonly Info --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="{{ $user->name }}" readonly disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" value="{{ $user->email }}" readonly disabled>
                                <small class="text-muted">Perubahan nama/email/password dilakukan di Portal.</small>
                            </div>

                            {{-- Role --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    {{-- Ambil role dari localProfile (Bridge) --}}
                                    @php
                                        $currentRole = $user->localProfile
                                            ? $user->localProfile->roles->first()?->name
                                            : '';
                                    @endphp

                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}"
                                            {{ $currentRole == $role->name ? 'selected' : '' }}>
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
                                            @php
                                                // Ambil ID Restoran dari Local Profile
                                                $assignedIds = $user->localProfile
                                                    ? $user->localProfile->restaurants->pluck('id')->toArray()
                                                    : [];
                                            @endphp

                                            @foreach ($restaurants as $rest)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="restaurants[]"
                                                            value="{{ $rest->id }}" id="rest_{{ $rest->id }}"
                                                            {{ in_array($rest->id, $assignedIds) ? 'checked' : '' }}>
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
                            <button type="submit" class="btn btn-primary">Update Access</button>
                            <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
