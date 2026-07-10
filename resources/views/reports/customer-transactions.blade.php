@extends('layouts.app')

@section('title', 'Transaksi per Pelanggan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')

    <div class="ibox">
        <div class="ibox-title"><h5>Transaksi per Pelanggan</h5></div>
        <div class="ibox-content">
            <form method="GET" class="row m-b">
                <div class="col-md-6">
                    <label>Pelanggan</label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">Pilih pelanggan</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((int) request('customer_id') === $customer->id)>
                                {{ $customer->name }}{{ $customer->phone ? ' - '.$customer->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button class="btn btn-white btn-block"><i class="fa fa-filter"></i> Filter</button>
                </div>
            </form>

            @if ($selectedCustomer)
                <div class="row">
                    <div class="col-md-4">
                        <div class="widget style1 navy-bg">
                            <div class="row">
                                <div class="col-xs-4"><i class="fa fa-shopping-cart fa-3x"></i></div>
                                <div class="col-xs-8 text-right"><span>Transaksi</span><h2 class="font-bold">{{ number_format($summary['transactions']) }}</h2></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="widget style1 lazur-bg">
                            <div class="row">
                                <div class="col-xs-4"><i class="fa fa-tint fa-3x"></i></div>
                                <div class="col-xs-8 text-right"><span>Total Galon</span><h2 class="font-bold">{{ number_format($summary['qty']) }}</h2></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="widget style1 blue-bg">
                            <div class="row">
                                <div class="col-xs-4"><i class="fa fa-money fa-3x"></i></div>
                                <div class="col-xs-8 text-right"><span>Total Belanja</span><h2 class="font-bold">Rp {{ number_format($summary['income'], 0, ',', '.') }}</h2></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>

                <div class="row m-b">
                    <div class="col-md-6">
                        <h3 class="m-t-none">{{ $selectedCustomer->name }}</h3>
                        <p class="text-muted m-b-none">
                            {{ $selectedCustomer->phone ?: '-' }} | {{ $selectedCustomer->addressLabel() }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped no-datatable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Mobil</th>
                            <th>Input Oleh</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at?->format('d/m/Y H:i') }}</td>
                                <td>{{ $transaction->transaction_code }}</td>
                                <td>{{ $transaction->deliveryRun?->label() ?: '-' }}</td>
                                <td>{{ $transaction->creator?->name ?: '-' }}</td>
                                <td class="text-right">{{ number_format($transaction->qty) }}</td>
                                <td class="text-right">Rp {{ number_format($transaction->price, 0, ',', '.') }}</td>
                                <td class="text-right"><strong>Rp {{ number_format($transaction->total(), 0, ',', '.') }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    {{ $selectedCustomer ? 'Belum ada transaksi untuk pelanggan ini.' : 'Pilih pelanggan untuk melihat riwayat transaksi.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($selectedCustomer)
                {{ $transactions->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
