@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title"><h5>Transaksi</h5></div>
        <div class="ibox-content">
            <form method="GET" class="row m-b">
                <div class="col-sm-3"><input type="month" name="month" class="form-control" value="{{ $month }}"></div>
                <div class="col-sm-2"><button class="btn btn-white"><i class="fa fa-filter"></i> Filter</button></div>
                <div class="col-sm-7 text-right"><h3 class="m-n">Total Bulan Ini: Rp {{ number_format($total, 0, ',', '.') }}</h3></div>
            </form>
            <form method="POST" action="{{ route('transactions.store') }}" class="row m-b" id="transactionForm">@csrf
                <div class="col-sm-3"><select name="customer_id" id="transactionCustomer" class="form-control select2" required><option value="">Pilih pelanggan</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" data-price="{{ $customer->effectivePrice() }}">{{ $customer->name }} - Rp {{ number_format($customer->effectivePrice(), 0, ',', '.') }}</option>@endforeach</select></div>
                <div class="col-sm-2"><select name="delivery_run_id" class="form-control select2"><option value="">Mobil berjalan</option>@foreach($deliveryRuns as $run)<option value="{{ $run->id }}">{{ $run->label() }}</option>@endforeach</select></div>
                <div class="col-sm-2"><input name="qty" type="number" min="1" class="form-control" value="1" required></div>
                <div class="col-sm-2"><input name="price" id="transactionPrice" type="number" min="0" class="form-control" placeholder="Harga" required></div>
                <div class="col-sm-2"><input name="created_at" id="transactionTime" type="datetime-local" class="form-control" value="{{ $serverNow }}"></div>
                <div class="col-sm-1"><button class="btn btn-primary btn-block"><i class="fa fa-plus"></i></button></div>
                <div class="col-sm-12 m-t-xs">
                    <label><input type="checkbox" name="use_server_time" id="transactionUseServerTime" value="1" checked> Gunakan waktu server</label>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Tanggal</th><th>Kode</th><th>Pelanggan</th><th>Mobil</th><th>Qty</th><th>Harga</th><th>Total</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at?->format('d/m/Y H:i') }}</td>
                                <td>{{ $transaction->transaction_code }}</td>
                                <td>{{ $transaction->customer?->name ?: $transaction->customer_name_snapshot ?: '-' }}</td>
                                <td>{{ $transaction->deliveryRun?->label() ?: '-' }}</td>
                                <td>{{ $transaction->qty }}</td>
                                <td>Rp {{ number_format($transaction->price, 0, ',', '.') }}</td>
                                <td><strong>Rp {{ number_format($transaction->total(), 0, ',', '.') }}</strong></td>
                                <td class="text-right">
                                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i></a>
                                    <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" style="display:inline" onsubmit="return confirm('Batalkan transaksi ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">Belum ada transaksi pada bulan ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    (function () {
        const customer = $('#transactionCustomer');
        const price = document.getElementById('transactionPrice');
        const time = document.getElementById('transactionTime');
        const useServerTime = document.getElementById('transactionUseServerTime');

        customer.on('change', function () {
            const selectedPrice = customer.find(':selected').data('price');
            if (selectedPrice !== undefined) {
                price.value = selectedPrice;
            }
        });

        if (useServerTime && time) {
            const toggleTime = () => {
                time.disabled = useServerTime.checked;
            };

            useServerTime.addEventListener('change', toggleTime);
            toggleTime();
        }
    }());
</script>
@endsection
