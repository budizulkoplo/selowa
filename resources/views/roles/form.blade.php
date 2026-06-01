@extends('layouts.app')

@section('title', $role->exists ? 'Edit Role' : 'Tambah Role')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title"><h5>{{ $role->exists ? 'Edit Role' : 'Tambah Role' }}</h5></div>
        <div class="ibox-content">
            <form method="POST" action="{{ $role->exists ? route('roles.update', $role) : route('roles.store') }}">
                @csrf
                @if ($role->exists) @method('PUT') @endif
                <div class="form-group">
                    <label>Nama Role</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
                </div>
                <div class="form-group">
                    <label>Guard</label>
                    <input type="text" name="guard_name" class="form-control" value="{{ old('guard_name', $role->guard_name ?: 'web') }}">
                </div>
                <a href="{{ route('roles.index') }}" class="btn btn-white">Kembali</a>
                <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
