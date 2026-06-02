@extends('layouts.app')

@section('title', 'Alamat Pelanggan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')

    <div class="row">
        <div class="col-lg-4">
            <div class="ibox address-panel">
                <div class="ibox-title"><h5>Tambah Kota</h5></div>
                <div class="ibox-content">
                    <form method="POST" action="{{ route('customer-addresses.cities.store') }}">
                        @csrf
                        <div class="form-group"><label>Nama Kota</label><input name="name" class="form-control" placeholder="Contoh: Kendal" required></div>
                        <button class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Simpan Kota</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox address-panel">
                <div class="ibox-title"><h5>Tambah Kecamatan</h5></div>
                <div class="ibox-content">
                    <form method="POST" action="{{ route('customer-addresses.districts.store') }}">
                        @csrf
                        <div class="form-group"><label>Kota</label><select name="city_id" class="form-control" required><option value="">Pilih kota</option>@foreach($cities as $city)<option value="{{ $city->id }}">{{ $city->name }}</option>@endforeach</select></div>
                        <div class="form-group"><label>Nama Kecamatan</label><input name="name" class="form-control" placeholder="Contoh: Kaliwungu" required></div>
                        <button class="btn btn-info btn-block"><i class="fa fa-plus"></i> Simpan Kecamatan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox address-panel">
                <div class="ibox-title"><h5>Tambah Desa</h5></div>
                <div class="ibox-content">
                    <form method="POST" action="{{ route('customer-addresses.villages.store') }}">
                        @csrf
                        <div class="form-group"><label>Kecamatan</label><select name="district_id" class="form-control" required><option value="">Pilih kecamatan</option>@foreach($districts as $district)<option value="{{ $district->id }}">{{ $district->name }} - {{ $district->city?->name }}</option>@endforeach</select></div>
                        <div class="form-group"><label>Nama Desa</label><input name="name" class="form-control" placeholder="Contoh: Kutoharjo" required></div>
                        <button class="btn btn-success btn-block"><i class="fa fa-plus"></i> Simpan Desa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-title"><h5>Kelola Alamat</h5></div>
        <div class="ibox-content">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab-cities">Kota</a></li>
                <li><a data-toggle="tab" href="#tab-districts">Kecamatan</a></li>
                <li><a data-toggle="tab" href="#tab-villages">Desa</a></li>
            </ul>
            <div class="tab-content p-md">
                <div id="tab-cities" class="tab-pane active">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead><tr><th>Nama Kota</th><th>Kecamatan</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                            <tbody>
                                @foreach($cities as $city)
                                    <tr>
                                        <td>
                                            <form id="city-form-{{ $city->id }}" method="POST" action="{{ route('customer-addresses.cities.update', $city) }}" class="address-inline-form">
                                                @csrf @method('PUT')
                                                <input name="name" class="form-control" value="{{ $city->name }}" required>
                                                <input type="hidden" name="is_active" value="{{ $city->is_active ? 1 : 0 }}">
                                            </form>
                                        </td>
                                        <td>{{ $city->districts->count() }}</td>
                                        <td><span class="label label-{{ $city->is_active ? 'primary' : 'default' }}">{{ $city->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                        <td class="text-right">
                                            <button form="city-form-{{ $city->id }}" class="btn btn-primary btn-sm"><i class="fa fa-save"></i></button>
                                            <form method="POST" action="{{ route('customer-addresses.cities.destroy', $city) }}" style="display:inline" onsubmit="return confirm('Nonaktifkan kota ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa fa-ban"></i></button></form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="tab-districts" class="tab-pane">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead><tr><th>Kota</th><th>Nama Kecamatan</th><th>Desa</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                            <tbody>
                                @foreach($districts as $district)
                                    <tr>
                                        <td>
                                            <form id="district-form-{{ $district->id }}" method="POST" action="{{ route('customer-addresses.districts.update', $district) }}" class="address-inline-form">
                                                @csrf @method('PUT')
                                                <select name="city_id" class="form-control" required>
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}" @selected($district->city_id === $city->id)>{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="is_active" value="{{ $district->is_active ? 1 : 0 }}">
                                            </form>
                                        </td>
                                        <td><input form="district-form-{{ $district->id }}" name="name" class="form-control" value="{{ $district->name }}" required></td>
                                        <td>{{ $district->villages->count() }}</td>
                                        <td><span class="label label-{{ $district->is_active ? 'primary' : 'default' }}">{{ $district->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                        <td class="text-right">
                                            <button form="district-form-{{ $district->id }}" class="btn btn-primary btn-sm"><i class="fa fa-save"></i></button>
                                            <form method="POST" action="{{ route('customer-addresses.districts.destroy', $district) }}" style="display:inline" onsubmit="return confirm('Nonaktifkan kecamatan ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa fa-ban"></i></button></form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="tab-villages" class="tab-pane">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead><tr><th>Kecamatan</th><th>Nama Desa</th><th>Kota</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                            <tbody>
                                @foreach($villages as $village)
                                    <tr>
                                        <td>
                                            <form id="village-form-{{ $village->id }}" method="POST" action="{{ route('customer-addresses.villages.update', $village) }}" class="address-inline-form">
                                                @csrf @method('PUT')
                                                <select name="district_id" class="form-control" required>
                                                    @foreach($districts as $district)
                                                        <option value="{{ $district->id }}" @selected($village->district_id === $district->id)>{{ $district->name }} - {{ $district->city?->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="is_active" value="{{ $village->is_active ? 1 : 0 }}">
                                            </form>
                                        </td>
                                        <td><input form="village-form-{{ $village->id }}" name="name" class="form-control" value="{{ $village->name }}" required></td>
                                        <td>{{ $village->district?->city?->name ?: '-' }}</td>
                                        <td><span class="label label-{{ $village->is_active ? 'primary' : 'default' }}">{{ $village->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                        <td class="text-right">
                                            <button form="village-form-{{ $village->id }}" class="btn btn-primary btn-sm"><i class="fa fa-save"></i></button>
                                            <form method="POST" action="{{ route('customer-addresses.villages.destroy', $village) }}" style="display:inline" onsubmit="return confirm('Nonaktifkan desa ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa fa-ban"></i></button></form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<style>
    .address-panel .ibox-title {
        border-top: 3px solid #1ab394;
    }

    .address-inline-form {
        margin: 0;
    }
</style>
@endsection
