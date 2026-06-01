@extends('layouts.app')

@section('title', 'Pelanggan Setia')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox"><div class="ibox-title"><h5>Pelanggan Setia</h5></div><div class="ibox-content">
        <table class="table table-striped"><thead><tr><th>Pelanggan</th><th>Telepon</th><th>Alamat</th><th>Total Qty</th><th>Total Belanja</th></tr></thead><tbody>
            @foreach($customers as $customer)
                <tr><td><strong>{{ $customer->name }}</strong></td><td>{{ $customer->phone ?: '-' }}</td><td>{{ $customer->addressLabel() }}</td><td>{{ $customer->total_qty ?? 0 }}</td><td>Rp {{ number_format($customer->total_spend ?? 0, 0, ',', '.') }}</td></tr>
            @endforeach
        </tbody></table>
        {{ $customers->links() }}
    </div></div>
</div>
@endsection
