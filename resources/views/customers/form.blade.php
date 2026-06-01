@extends('layouts.app')

@section('title', $customer->exists ? 'Edit Pelanggan' : 'Tambah Pelanggan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title"><h5>{{ $customer->exists ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}</h5></div>
        <div class="ibox-content">
            <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
                @csrf @if($customer->exists) @method('PUT') @endif
                <div class="row">
                    <div class="col-md-6 form-group"><label>Nama</label><input name="name" class="form-control" value="{{ old('name', $customer->name) }}" required></div>
                    <div class="col-md-3 form-group"><label>Telepon</label><input name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}"></div>
                    <div class="col-md-3 form-group"><label>Harga Pelanggan</label><input name="customer_price" type="number" min="0" class="form-control" value="{{ old('customer_price', $customer->customer_price ?? 0) }}" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Alamat</label>
                        <select name="village_id" class="form-control">
                            <option value="">Pilih alamat</option>
                            @foreach($villages as $village)
                                <option value="{{ $village->id }}" @selected((int) old('village_id', $customer->village_id) === $village->id)>{{ $village->name }} - {{ $village->district?->name }} - {{ $village->district?->city?->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group"><label>RT</label><input name="rt" class="form-control" value="{{ old('rt', $customer->rt) }}"></div>
                    <div class="col-md-2 form-group"><label>RW</label><input name="rw" class="form-control" value="{{ old('rw', $customer->rw) }}"></div>
                    <div class="col-md-2 form-group"><label>Jadwal</label><input name="timetable" class="form-control" value="{{ old('timetable', $customer->timetable) }}"></div>
                </div>
                <div class="checkbox"><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $customer->is_active))> Aktif</label></div>
                <a href="{{ route('customers.index') }}" class="btn btn-white">Kembali</a>
                <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
