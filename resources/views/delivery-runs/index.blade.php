@extends('layouts.app')

@section('title', 'Mobil Berjalan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')

    <div class="row">
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title"><h5>Tambah Mobil</h5></div>
                <div class="ibox-content">
                    <form method="POST" action="{{ route('delivery-runs.vehicles.store') }}">
                        @csrf
                        <div class="form-group"><label>Nama Mobil</label><input name="name" class="form-control" placeholder="Mobil 1" required></div>
                        <div class="form-group"><label>No Polisi</label><input name="plate_number" class="form-control"></div>
                        <div class="form-group"><label>Driver Default</label><input name="driver_name" class="form-control"></div>
                        <div class="checkbox"><label><input type="checkbox" name="is_active" value="1" checked> Aktif</label></div>
                        <button class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Mobil</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title"><h5>Buat Mobil Berjalan</h5></div>
                <div class="ibox-content">
                    <form method="POST" action="{{ route('delivery-runs.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Mobil</label>
                                <select name="delivery_vehicle_id" class="form-control" required>
                                    <option value="">Pilih mobil</option>
                                    @foreach ($vehicles->where('is_active', true) as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->name }} {{ $vehicle->plate_number ? '- '.$vehicle->plate_number : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group"><label>Tanggal Jalan</label><input name="run_date" type="date" class="form-control" value="{{ now()->toDateString() }}" required></div>
                            <div class="col-md-3 form-group"><label>Driver</label><input name="driver_name" class="form-control"></div>
                            <div class="col-md-2 form-group"><label>Area</label><input name="area" class="form-control"></div>
                        </div>
                        <div class="form-group"><label>Catatan</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                        <button class="btn btn-primary"><i class="fa fa-road"></i> Buat Jadwal Jalan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-title"><h5>Daftar Mobil Berjalan</h5></div>
        <div class="ibox-content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Tanggal</th><th>Mobil</th><th>Driver</th><th>Area</th><th>Transaksi</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($runs as $run)
                            <tr>
                                <td>{{ $run->run_date?->format('d/m/Y') }}</td>
                                <td>{{ $run->vehicle?->name ?: '-' }}<br><small class="text-muted">{{ $run->vehicle?->plate_number }}</small></td>
                                <td>{{ $run->driver_name ?: $run->vehicle?->driver_name ?: '-' }}</td>
                                <td>{{ $run->area ?: '-' }}</td>
                                <td>{{ number_format($run->transactions_count) }}</td>
                                <td><span class="label label-{{ $run->status ? 'primary' : 'default' }}">{{ $run->status ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-right">
                                    <form method="POST" action="{{ route('delivery-runs.destroy', $run) }}" style="display:inline" onsubmit="return confirm('Nonaktifkan jadwal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">Belum ada mobil berjalan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $runs->links() }}
        </div>
    </div>
</div>
@endsection
