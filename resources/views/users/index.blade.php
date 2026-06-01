@extends('layouts.app')

@section('title', 'User')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title">
            <h5>User</h5>
            <div class="ibox-tools"><a href="{{ route('users.create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Tambah</a></div>
        </div>
        <div class="ibox-content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</td>
                                <td><span class="label label-{{ $user->is_active ? 'primary' : 'default' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-right">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i></a>
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" style="display:inline" onsubmit="return confirm('Hapus user ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">Belum ada user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
