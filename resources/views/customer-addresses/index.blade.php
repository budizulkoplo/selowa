@extends('layouts.app')

@section('title', 'Alamat Pelanggan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="row">
        <div class="col-md-4">
            <div class="ibox"><div class="ibox-title"><h5>Tambah Kota</h5></div><div class="ibox-content">
                <form method="POST" action="{{ route('customer-addresses.cities.store') }}">@csrf
                    <div class="form-group"><input name="name" class="form-control" placeholder="Nama kota" required></div>
                    <button class="btn btn-primary btn-block">Simpan Kota</button>
                </form>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="ibox"><div class="ibox-title"><h5>Tambah Kecamatan</h5></div><div class="ibox-content">
                <form method="POST" action="{{ route('customer-addresses.districts.store') }}">@csrf
                    <div class="form-group"><select name="city_id" class="form-control" required><option value="">Pilih kota</option>@foreach($cities as $city)<option value="{{ $city->id }}">{{ $city->name }}</option>@endforeach</select></div>
                    <div class="form-group"><input name="name" class="form-control" placeholder="Nama kecamatan" required></div>
                    <button class="btn btn-primary btn-block">Simpan Kecamatan</button>
                </form>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="ibox"><div class="ibox-title"><h5>Tambah Desa</h5></div><div class="ibox-content">
                <form method="POST" action="{{ route('customer-addresses.villages.store') }}">@csrf
                    <div class="form-group"><select name="district_id" class="form-control" required><option value="">Pilih kecamatan</option>@foreach($districts as $district)<option value="{{ $district->id }}">{{ $district->name }} - {{ $district->city?->name }}</option>@endforeach</select></div>
                    <div class="form-group"><input name="name" class="form-control" placeholder="Nama desa" required></div>
                    <button class="btn btn-primary btn-block">Simpan Desa</button>
                </form>
            </div></div>
        </div>
    </div>
    <div class="ibox">
        <div class="ibox-title"><h5>Data Alamat</h5></div>
        <div class="ibox-content">
            @foreach($cities as $city)
                <h4>{{ $city->name }}</h4>
                <div class="table-responsive m-b">
                    <table class="table table-bordered">
                        <thead><tr><th>Kecamatan</th><th>Desa</th></tr></thead>
                        <tbody>
                            @forelse($city->districts as $district)
                                <tr>
                                    <td>{{ $district->name }}</td>
                                    <td>{{ $district->villages->pluck('name')->join(', ') ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted">Belum ada kecamatan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
