@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')

    <div class="row">
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title"><h5>Profile User</h5></div>
                <div class="ibox-content text-center">
                    <img src="{{ $user->photo ? asset('storage/'.$user->photo) : asset('selowa.webp') }}" alt="Foto profile" style="width: 132px; height: 132px; object-fit: cover; border-radius: 50%; border: 4px solid #f3f3f4;">
                    <h3 class="m-t-md">{{ $user->name }}</h3>
                    <p class="text-muted">{{ $user->email }}</p>
                    <p>
                        @foreach ($user->roles as $role)
                            <span class="label label-primary">{{ $role->name }}</span>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title"><h5>Update Profile</h5></div>
                <div class="ibox-content">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Nama</label>
                                <input name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Email</label>
                                <input name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Foto</label>
                            <input name="photo" type="file" class="form-control" accept="image/*">
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Password Lama</label>
                                <input name="current_password" type="password" class="form-control" autocomplete="current-password">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Password Baru</label>
                                <input name="password" type="password" class="form-control" autocomplete="new-password">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Konfirmasi Password</label>
                                <input name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                            </div>
                        </div>
                        <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
