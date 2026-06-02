@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')

    <div class="ibox">
        <div class="ibox-title"><h5>Laporan Operasional</h5></div>
        <div class="ibox-content">
            <form method="GET" class="row m-b">
                <div class="col-md-3"><input name="from" type="date" class="form-control" value="{{ $from->toDateString() }}"></div>
                <div class="col-md-3"><input name="to" type="date" class="form-control" value="{{ $to->toDateString() }}"></div>
                <div class="col-md-2"><button class="btn btn-white"><i class="fa fa-filter"></i> Filter</button></div>
            </form>

            <div class="row">
                <div class="col-md-3"><div class="widget style1 navy-bg"><div class="row"><div class="col-xs-4"><i class="fa fa-shopping-cart fa-3x"></i></div><div class="col-xs-8 text-right"><span>Transaksi</span><h2 class="font-bold">{{ number_format($summary['transactions']) }}</h2></div></div></div></div>
                <div class="col-md-3"><div class="widget style1 lazur-bg"><div class="row"><div class="col-xs-4"><i class="fa fa-tint fa-3x"></i></div><div class="col-xs-8 text-right"><span>Galon Keluar</span><h2 class="font-bold">{{ number_format($summary['qty']) }}</h2></div></div></div></div>
                <div class="col-md-3"><div class="widget style1 yellow-bg"><div class="row"><div class="col-xs-4"><i class="fa fa-users fa-3x"></i></div><div class="col-xs-8 text-right"><span>Pelanggan</span><h2 class="font-bold">{{ number_format($summary['customers']) }}</h2></div></div></div></div>
                <div class="col-md-3"><div class="widget style1 blue-bg"><div class="row"><div class="col-xs-4"><i class="fa fa-money fa-3x"></i></div><div class="col-xs-8 text-right"><span>Pendapatan</span><h2 class="font-bold">Rp {{ number_format($summary['income'], 0, ',', '.') }}</h2></div></div></div></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Rekap Harian</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('reports.export', ['section' => 'daily', 'format' => 'excel'] + request()->query()) }}" class="btn btn-success btn-xs"><i class="fa fa-file-excel-o"></i> Excel</a>
                        <a href="{{ route('reports.export', ['section' => 'daily', 'format' => 'pdf'] + request()->query()) }}" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th>Tanggal</th><th>Transaksi</th><th>Galon</th><th>Pendapatan</th></tr></thead>
                            <tbody>
                                @foreach ($dailyRows as $row)
                                    <tr><td>{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td><td>{{ number_format($row->transaction_count) }}</td><td>{{ number_format($row->qty_total) }}</td><td>Rp {{ number_format($row->income_total, 0, ',', '.') }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Pelanggan Teratas</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('reports.export', ['section' => 'customers', 'format' => 'excel'] + request()->query()) }}" class="btn btn-success btn-xs"><i class="fa fa-file-excel-o"></i> Excel</a>
                        <a href="{{ route('reports.export', ['section' => 'customers', 'format' => 'pdf'] + request()->query()) }}" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th>Pelanggan</th><th>Telp</th><th>Galon</th><th>Pendapatan</th></tr></thead>
                            <tbody>
                                @foreach ($customerRows as $row)
                                    <tr><td>{{ $row->name }}</td><td>{{ $row->phone ?: '-' }}</td><td>{{ number_format($row->qty_total) }}</td><td>Rp {{ number_format($row->income_total, 0, ',', '.') }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Laporan Mobil Berjalan</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('reports.export', ['section' => 'vehicles', 'format' => 'excel'] + request()->query()) }}" class="btn btn-success btn-xs"><i class="fa fa-file-excel-o"></i> Excel</a>
                        <a href="{{ route('reports.export', ['section' => 'vehicles', 'format' => 'pdf'] + request()->query()) }}" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th>Tanggal</th><th>Mobil</th><th>Driver</th><th>Area</th><th>Transaksi</th><th>Galon</th></tr></thead>
                            <tbody>
                                @foreach ($vehicleRows as $row)
                                    <tr><td>{{ $row->run_date?->format('d/m/Y') }}</td><td>{{ $row->vehicle?->name ?: '-' }}</td><td>{{ $row->driver_name ?: $row->vehicle?->driver_name ?: '-' }}</td><td>{{ $row->area ?: '-' }}</td><td>{{ number_format($row->transactions_count) }}</td><td>{{ number_format($row->qty_total ?: 0) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Stok Galon</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('reports.export', ['section' => 'gallons', 'format' => 'excel'] + request()->query()) }}" class="btn btn-success btn-xs"><i class="fa fa-file-excel-o"></i> Excel</a>
                        <a href="{{ route('reports.export', ['section' => 'gallons', 'format' => 'pdf'] + request()->query()) }}" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th>Nama</th><th>Stok</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach ($gallons as $gallon)
                                    <tr><td>{{ $gallon->name }}</td><td>{{ number_format($gallon->stock) }}</td><td>{{ $gallon->is_active ? 'Aktif' : 'Nonaktif' }}</td></tr>
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
