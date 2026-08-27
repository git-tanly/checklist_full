@extends('layouts.mantis')

@section('header')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Add New Employee</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item" aria-current="page">Create</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">



            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5>Detail Pegawai</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('employees.store') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label">Nama Pegawai</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Rafly Akbar"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Posisi</label>
                                <select name="position" class="form-control" required>
                                    <option value="" disabled selected>Pilih Posisi...</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position }}" {{ old('position') == $position ? 'selected' : '' }}>
                                            {{ $position }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label">Restoran</label>
                                <select name="restaurant_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Restoran...</option>
                                    @foreach ($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}" {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                            {{ $restaurant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Simpan Pegawai</button>
                                <a href="{{ route('employees.index') }}" class="btn btn-light">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>
@endsection
