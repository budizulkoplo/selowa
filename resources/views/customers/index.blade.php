@extends('layouts.app')

@section('title', 'Pelanggan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title">
            <h5>Pelanggan</h5>
            <div class="ibox-tools"><a href="{{ route('customers.create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Tambah</a></div>
        </div>
        <div class="ibox-content">
            <form class="row m-b" method="GET">
                <div class="col-sm-5"><input name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari nama atau telepon"></div>
                <div class="col-sm-2"><button class="btn btn-white"><i class="fa fa-search"></i> Cari</button></div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Nama</th><th>Telepon</th><th>Alamat</th><th>Jadwal</th><th>Harga</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td><strong>{{ $customer->name }}</strong></td>
                                <td>{{ $customer->phone ?: '-' }}</td>
                                <td>{{ $customer->addressLabel() }}</td>
                                <td>{{ $customer->timetable ?: '-' }}</td>
                                <td>Rp {{ number_format($customer->customer_price, 0, ',', '.') }}</td>
                                <td><span class="label label-{{ $customer->is_active ? 'primary' : 'default' }}">{{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-right">
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i></a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" style="display:inline" onsubmit="return confirm('Nonaktifkan pelanggan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">Belum ada pelanggan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
