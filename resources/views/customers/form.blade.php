@extends('layouts.app')

@section('title', $customer->exists ? 'Edit Pelanggan' : 'Tambah Pelanggan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title"><h5>{{ $customer->exists ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}</h5></div>
        <div class="ibox-content">
            @if($customer->exists)
                <div class="alert alert-info">
                    Registrasi: {{ optional($customer->registered_at ?? $customer->created_at)->format('d/m/Y H:i') ?: '-' }}
                    oleh {{ $customer->registeredBy?->name ?: '-' }}
                </div>
            @endif
            <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
                @csrf @if($customer->exists) @method('PUT') @endif
                <div class="row">
                    <div class="col-md-6 form-group"><label>Nama</label><input name="name" class="form-control" value="{{ old('name', $customer->name) }}" required></div>
                    <div class="col-md-3 form-group"><label>Telepon</label><input name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}"></div>
                    <div class="col-md-3 form-group"><label>Harga Pelanggan</label><input name="customer_price" type="text" inputmode="numeric" class="form-control money-input" value="{{ old('customer_price', $customer->customer_price ?? 0) }}" required></div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Member</label>
                        <div class="checkbox m-t-xs"><label><input type="checkbox" name="is_member" value="1" @checked(old('is_member', $customer->is_member))> Aktifkan diskon member</label></div>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Tipe Diskon</label>
                        <select name="discount_type" class="form-control">
                            <option value="none" @selected(old('discount_type', $customer->discount_type) === 'none')>Tanpa diskon</option>
                            <option value="amount" @selected(old('discount_type', $customer->discount_type) === 'amount')>Nominal</option>
                            <option value="percent" @selected(old('discount_type', $customer->discount_type) === 'percent')>Persen</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group"><label>Nilai Diskon</label><input name="discount_value" type="text" inputmode="numeric" class="form-control money-input" value="{{ old('discount_value', $customer->discount_value ?? 0) }}"></div>
                    <div class="col-md-3 form-group">
                        <label>Harga Efektif</label>
                        <p class="form-control-static">Rp {{ number_format($customer->effectivePrice(), 0, ',', '.') }}</p>
                    </div>
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
