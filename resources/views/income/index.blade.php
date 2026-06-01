@extends('layouts.app')

@section('title', 'Pendapatan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox"><div class="ibox-title"><h5>Pendapatan</h5></div><div class="ibox-content">
        <form method="GET" class="row m-b">
            <div class="col-sm-3"><input type="date" name="from" class="form-control" value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}"></div>
            <div class="col-sm-3"><input type="date" name="to" class="form-control" value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}"></div>
            <div class="col-sm-2"><button class="btn btn-white"><i class="fa fa-filter"></i> Filter</button></div>
            <div class="col-sm-4 text-right"><h3 class="m-n">Rp {{ number_format($rows->sum('income_total'), 0, ',', '.') }}</h3></div>
        </form>
        <table class="table table-striped"><thead><tr><th>Tanggal</th><th>Transaksi</th><th>Qty</th><th>Pendapatan</th></tr></thead><tbody>
            @foreach($rows as $row)<tr><td>{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td><td>{{ $row->transaction_count }}</td><td>{{ $row->qty_total }}</td><td>Rp {{ number_format($row->income_total, 0, ',', '.') }}</td></tr>@endforeach
        </tbody></table>
    </div></div>
</div>
@endsection
