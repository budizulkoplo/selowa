@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox"><div class="ibox-title"><h5>Edit Transaksi</h5></div><div class="ibox-content">
        <form method="POST" action="{{ route('transactions.update', $transaction) }}">
            @csrf @method('PUT')
            <div class="form-group"><label>Pelanggan</label><select name="customer_id" class="form-control" required>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected($transaction->customer_id === $customer->id)>{{ $customer->name }}</option>@endforeach</select></div>
            <div class="row">
                <div class="col-sm-4 form-group"><label>Qty</label><input name="qty" type="number" min="1" class="form-control" value="{{ $transaction->qty }}" required></div>
                <div class="col-sm-4 form-group"><label>Harga</label><input name="price" type="number" min="0" class="form-control" value="{{ $transaction->price }}" required></div>
                <div class="col-sm-4 form-group"><label>Tanggal</label><input name="created_at" type="datetime-local" class="form-control" value="{{ $transaction->created_at?->format('Y-m-d\\TH:i') }}"></div>
            </div>
            <a href="{{ route('transactions.index') }}" class="btn btn-white">Kembali</a>
            <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
        </form>
    </div></div>
</div>
@endsection
