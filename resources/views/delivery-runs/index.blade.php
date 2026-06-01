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
                        <div class="form-group">
                            <label>Admin / Driver</label>
                            <select name="user_id" class="form-control">
                                <option value="">Pilih admin</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group"><label>No Polisi</label><input name="plate_number" class="form-control"></div>
                        <div class="form-group"><label>Driver Manual</label><input name="driver_name" class="form-control" placeholder="Isi jika tidak memakai user admin"></div>
                        <div class="checkbox"><label><input type="checkbox" name="is_active" value="1" checked> Aktif</label></div>
                        <button class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Mobil</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title"><h5>Rute Mingguan</h5></div>
                <div class="ibox-content">
                    <form method="POST" action="{{ route('delivery-runs.routes.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Mobil</label>
                                <select name="delivery_vehicle_id" class="form-control" required>
                                    <option value="">Pilih mobil</option>
                                    @foreach ($vehicles->where('is_active', true) as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->name }} - {{ $vehicle->driverLabel() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Hari</label>
                                <select name="day_of_week" class="form-control" required>
                                    @foreach ($dayOptions as $dayValue => $dayLabel)
                                        <option value="{{ $dayValue }}">{{ $dayLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 form-group"><label>Nama Rute</label><input name="name" class="form-control" placeholder="Rute Senin Area Utara" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 form-group"><label>Area</label><input name="area" class="form-control" placeholder="Desa / kecamatan tujuan"></div>
                            <div class="col-md-7 form-group"><label>Catatan Rute</label><input name="notes" class="form-control" placeholder="Urutan atau catatan pengiriman"></div>
                        </div>
                        <div class="checkbox"><label><input type="checkbox" name="is_active" value="1" checked> Aktif</label></div>
                        <button class="btn btn-primary"><i class="fa fa-map"></i> Simpan Rute</button>
                    </form>
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-title"><h5>Buat Mobil Berjalan</h5></div>
                <div class="ibox-content">
                    <form method="POST" action="{{ route('delivery-runs.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-5 form-group">
                                <label>Rute Terjadwal</label>
                                <select name="delivery_route_id" id="deliveryRouteSelect" class="form-control">
                                    <option value="">Pilih rute mingguan</option>
                                    @foreach ($routes->where('is_active', true) as $route)
                                        <option value="{{ $route->id }}" data-vehicle="{{ $route->delivery_vehicle_id }}" data-driver="{{ $route->vehicle?->driverLabel() }}" data-area="{{ $route->area ?: $route->name }}">{{ $route->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Mobil</label>
                                <select name="delivery_vehicle_id" id="deliveryVehicleSelect" class="form-control">
                                    <option value="">Pilih mobil</option>
                                    @foreach ($vehicles->where('is_active', true) as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->name }} - {{ $vehicle->driverLabel() }} {{ $vehicle->plate_number ? '- '.$vehicle->plate_number : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group"><label>Tanggal Jalan</label><input name="run_date" type="date" class="form-control" value="{{ now()->toDateString() }}" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 form-group"><label>Admin / Driver</label><input name="driver_name" id="deliveryDriverInput" class="form-control" placeholder="Otomatis dari mobil/rute"></div>
                            <div class="col-md-7 form-group"><label>Area</label><input name="area" id="deliveryAreaInput" class="form-control"></div>
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
                    <thead><tr><th>Tanggal</th><th>Mobil</th><th>Admin</th><th>Rute</th><th>Area</th><th>Transaksi</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($runs as $run)
                            <tr>
                                <td>{{ $run->run_date?->format('d/m/Y') }}</td>
                                <td>{{ $run->vehicle?->name ?: '-' }}<br><small class="text-muted">{{ $run->vehicle?->plate_number }}</small></td>
                                <td>{{ $run->driver_name ?: $run->vehicle?->driverLabel() ?: '-' }}</td>
                                <td>{{ $run->route?->label() ?: '-' }}</td>
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
                            <tr><td colspan="8" class="text-center text-muted">Belum ada mobil berjalan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $runs->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    (function () {
        const routeSelect = $('#deliveryRouteSelect');
        const vehicleSelect = $('#deliveryVehicleSelect');
        const driverInput = document.getElementById('deliveryDriverInput');
        const areaInput = document.getElementById('deliveryAreaInput');

        routeSelect.on('change', function () {
            const selected = routeSelect.find(':selected');
            const vehicle = selected.data('vehicle');

            if (vehicle) {
                vehicleSelect.val(String(vehicle)).trigger('change');
            }

            driverInput.value = selected.data('driver') || '';
            areaInput.value = selected.data('area') || '';
        });
    }());
</script>
@endsection
