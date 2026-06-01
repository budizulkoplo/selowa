@extends('layouts.app')

@section('title', 'Role')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title">
            <h5>Role</h5>
            <div class="ibox-tools"><a href="{{ route('roles.create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Tambah</a></div>
        </div>
        <div class="ibox-content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Nama</th><th>Guard</th><th>User</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td><strong>{{ $role->name }}</strong></td>
                                <td>{{ $role->guard_name }}</td>
                                <td>{{ $role->users_count }}</td>
                                <td class="text-right">
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i></a>
                                    <form method="POST" action="{{ route('roles.destroy', $role) }}" style="display:inline" onsubmit="return confirm('Hapus role ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">Belum ada role.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection
