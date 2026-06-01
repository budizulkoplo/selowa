@extends('layouts.app')

@section('title', $user->exists ? 'Edit User' : 'Tambah User')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title"><h5>{{ $user->exists ? 'Edit User' : 'Tambah User' }}</h5></div>
        <div class="ibox-content">
            <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
                @csrf
                @if ($user->exists) @method('PUT') @endif
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="form-group">
                    <label>Password {{ $user->exists ? '(kosongkan jika tidak diganti)' : '' }}</label>
                    <input type="password" name="password" class="form-control" {{ $user->exists ? '' : 'required' }}>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <div class="row">
                        @foreach ($roles as $role)
                            <div class="col-sm-3">
                                <label><input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name, old('roles', $selectedRoles), true))> {{ $role->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))> Aktif</label>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-white">Kembali</a>
                <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
