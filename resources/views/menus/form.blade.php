@extends('layouts.app')

@section('title', $menu->exists ? 'Edit Menu' : 'Tambah Menu')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title"><h5>{{ $menu->exists ? 'Edit Menu' : 'Tambah Menu' }}</h5></div>
        <div class="ibox-content">
            <form method="POST" action="{{ $menu->exists ? route('menus.update', $menu) : route('menus.store') }}">
                @csrf
                @if ($menu->exists) @method('PUT') @endif
                <div class="form-group">
                    <label>Parent</label>
                    <select name="parent_id" class="form-control">
                        <option value="">Root menu</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" @selected((int) old('parent_id', $menu->parent_id) === $parent->id)>{{ $parent->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $menu->title) }}" required>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Route Name</label>
                            <input type="text" name="route_name" class="form-control" value="{{ old('route_name', $menu->route_name) }}" placeholder="users.index">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>URL</label>
                            <input type="text" name="url" class="form-control" value="{{ old('url', $menu->url) }}" placeholder="/contoh">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Icon FontAwesome</label>
                            <input type="text" name="icon" class="form-control" value="{{ old('icon', $menu->icon) }}" placeholder="fa-users">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Urutan</label>
                            <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $menu->sort_order) }}">
                        </div>
                    </div>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $menu->is_active))> Aktif</label>
                </div>
                <a href="{{ route('menus.index') }}" class="btn btn-white">Kembali</a>
                <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
