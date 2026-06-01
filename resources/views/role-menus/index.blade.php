@extends('layouts.app')

@section('title', 'Role Menu')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="row">
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title"><h5>Pilih Role</h5></div>
                <div class="ibox-content">
                    <form method="GET" action="{{ route('role-menus.index') }}">
                        <div class="form-group">
                            <select name="role_id" class="form-control" onchange="this.form.submit()">
                                @foreach ($roles as $item)
                                    <option value="{{ $item->id }}" @selected($role && $role->id === $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <a href="{{ route('menus.index') }}" class="btn btn-white btn-block"><i class="fa fa-sitemap"></i> CRUD Menu</a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title"><h5>Akses Menu {{ $role?->name }}</h5></div>
                <div class="ibox-content">
                    @if ($role)
                        <form method="POST" action="{{ route('role-menus.update', $role) }}">
                            @csrf @method('PUT')
                            <div class="menu-tree">
                                @foreach ($menus as $menu)
                                    @include('role-menus.node', ['menu' => $menu, 'selectedMenus' => $selectedMenus, 'level' => 0])
                                @endforeach
                            </div>
                            <button class="btn btn-primary m-t"><i class="fa fa-save"></i> Simpan Akses</button>
                        </form>
                    @else
                        <p class="text-muted">Buat role dulu untuk mengatur akses menu.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<style>
    .menu-tree label { font-weight: 400; margin-bottom: 8px; }
    .menu-tree .menu-node { padding: 7px 0; border-bottom: 1px solid #f3f3f4; }
</style>
@endsection
