@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title">
            <h5>Transaksi</h5>
            <div class="ibox-tools">
                <a href="{{ route('transactions.export', ['format' => 'excel'] + request()->query()) }}" class="btn btn-success btn-xs">
                    <i class="fa fa-file-excel-o"></i> Excel
                </a>
                <a href="{{ route('transactions.export', ['format' => 'pdf'] + request()->query()) }}" class="btn btn-danger btn-xs">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </a>
                <button type="button" class="btn btn-primary btn-xs" data-selowa-modal="#transactionModal">
                    <i class="fa fa-plus"></i> Tambah Transaksi
                </button>
            </div>
        </div>
        <div class="ibox-content">
            <form method="GET" class="row m-b">
                @if ($canSeeMonthly)
                    <div class="col-sm-3"><input type="month" name="month" class="form-control" value="{{ $month }}"></div>
                    <div class="col-sm-2"><button class="btn btn-white"><i class="fa fa-filter"></i> Filter</button></div>
                    <div class="col-sm-7 text-right"><h3 class="m-n">{{ $periodLabel }}: Rp {{ number_format($total, 0, ',', '.') }}</h3></div>
                @else
                    <div class="col-sm-12 text-right"><h3 class="m-n">{{ $periodLabel }}: Rp {{ number_format($total, 0, ',', '.') }}</h3></div>
                @endif
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
                                    @if (auth()->user()?->hasAnyRole(['owner', 'superadmin']))
                                        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" style="display:inline" onsubmit="return confirm('Batalkan transaksi ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></form>
                                    @endif
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

@include('transactions._modal-form', [
    'modalId' => 'transactionModal',
    'formId' => 'transactionForm',
    'customerSelectId' => 'transactionCustomer',
    'priceInputId' => 'transactionPrice',
    'timeInputId' => 'transactionTime',
    'useServerId' => 'transactionUseServerTime',
])
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
                price.value = window.SelowaMoney ? window.SelowaMoney.format(selectedPrice) : selectedPrice;
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
