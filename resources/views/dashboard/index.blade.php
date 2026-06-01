@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')

    <div class="row">
        @foreach ($summaryCards as $card)
            <div class="col-lg-3 col-md-6">
                <div class="ibox">
                    <div class="ibox-content">
                        <h5 class="text-muted">{{ $card['label'] }}</h5>
                        <div class="stat-card">
                            <div>
                                <h2 class="no-margins text-{{ $card['color'] }}">{{ $card['value'] }}</h2>
                                <small class="text-muted">{{ $card['subtext'] }}</small>
                            </div>
                            <div class="stat-icon bg-{{ $card['color'] }}">
                                <i class="fa {{ $card['icon'] }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="ibox">
        <div class="ibox-title">
            <h5>Buat Transaksi</h5>
            <div class="ibox-tools">
                <a href="{{ route('transactions.index') }}" class="btn btn-white btn-xs"><i class="fa fa-list"></i> Daftar Transaksi</a>
            </div>
        </div>
        <div class="ibox-content">
            <form method="POST" action="{{ route('transactions.store') }}" id="quickTransactionForm" class="m-b">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Pelanggan</label>
                        <select name="customer_id" id="dashboardCustomer" class="form-control select2" required>
                            <option value="">Pilih pelanggan</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" data-price="{{ $customer->customer_price }}">{{ $customer->name }} - {{ $customer->phone ?: 'Tanpa telp' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Qty</label>
                        <input name="qty" type="number" min="1" class="form-control" value="1" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Harga</label>
                        <input name="price" id="dashboardPrice" type="number" min="0" class="form-control" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Waktu</label>
                        <input name="created_at" id="dashboardTransactionTime" type="datetime-local" class="form-control" value="{{ $serverNow }}">
                    </div>
                    <div class="col-md-2 form-group">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Simpan</button>
                    </div>
                </div>
                <div class="checkbox m-t-none">
                    <label><input type="checkbox" name="use_server_time" id="dashboardUseServerTime" value="1" checked> Gunakan waktu server</label>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dashboardCustomerTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Jadwal Pengiriman</th>
                            <th>Harga Pelanggan</th>
                            <th>No Telp</th>
                            <th>Kota</th>
                            <th>Kecamatan</th>
                            <th>Desa</th>
                            <th>RT / RW</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr class="customer-picker-row"
                                data-customer-id="{{ $customer->id }}"
                                data-price="{{ $customer->customer_price }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $customer->name }}</strong></td>
                                <td>{{ $customer->timetable ?: '-' }}</td>
                                <td>Rp {{ number_format($customer->customer_price, 0, ',', '.') }}</td>
                                <td>{{ $customer->phone ?: '-' }}</td>
                                <td>{{ $customer->village?->district?->city?->name ?: '-' }}</td>
                                <td>{{ $customer->village?->district?->name ?: '-' }}</td>
                                <td>{{ $customer->village?->name ?: '-' }}</td>
                                <td>{{ ($customer->rt ?: '-') . ' / ' . ($customer->rw ?: '-') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<style>
    .stat-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
    }

    .customer-picker-row {
        cursor: pointer;
    }
</style>
<script>
    (function () {
        const customerSelect = $('#dashboardCustomer');
        const priceInput = document.getElementById('dashboardPrice');
        const timeInput = document.getElementById('dashboardTransactionTime');
        const useServerTime = document.getElementById('dashboardUseServerTime');

        function syncPriceFromSelected() {
            const price = customerSelect.find(':selected').data('price');
            if (price !== undefined) {
                priceInput.value = price;
            }
        }

        customerSelect.on('change', syncPriceFromSelected);

        $('#dashboardCustomerTable tbody').on('click', 'tr.customer-picker-row', function () {
            customerSelect.val(this.dataset.customerId).trigger('change');
            priceInput.value = this.dataset.price || '';
            document.getElementById('quickTransactionForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        if (useServerTime && timeInput) {
            const toggleTime = () => {
                timeInput.disabled = useServerTime.checked;
            };

            useServerTime.addEventListener('change', toggleTime);
            toggleTime();
        }
    }());
</script>
@endsection
