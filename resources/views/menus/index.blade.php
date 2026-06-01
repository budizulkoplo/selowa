@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title">
            <h5>Menu</h5>
            <div class="ibox-tools"><a href="{{ route('menus.create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Tambah</a></div>
        </div>
        <div class="ibox-content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Judul</th><th>Parent</th><th>Route/URL</th><th>Urutan</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($menus as $menu)
                            <tr>
                                <td><i class="fa {{ $menu->icon ?: 'fa-circle-o' }}"></i> <strong>{{ $menu->title }}</strong></td>
                                <td>{{ $menu->parent?->title ?: '-' }}</td>
                                <td>{{ $menu->route_name ?: $menu->url ?: '-' }}</td>
                                <td>{{ $menu->sort_order }}</td>
                                <td><span class="label label-{{ $menu->is_active ? 'primary' : 'default' }}">{{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-right">
                                    <a href="{{ route('menus.edit', $menu) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i></a>
                                    <form method="POST" action="{{ route('menus.destroy', $menu) }}" style="display:inline" onsubmit="return confirm('Hapus menu ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">Belum ada menu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $menus->links() }}
        </div>
    </div>
</div>
@endsection
