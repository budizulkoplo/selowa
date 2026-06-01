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
                <button type="button" class="btn btn-primary btn-xs" data-selowa-modal="#dashboardTransactionModal"><i class="fa fa-plus"></i> Input Transaksi</button>
                <a href="{{ route('transactions.index') }}" class="btn btn-white btn-xs"><i class="fa fa-list"></i> Daftar Transaksi</a>
            </div>
        </div>
        <div class="ibox-content">
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
                                data-price="{{ $customer->effectivePrice() }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $customer->name }}</strong></td>
                                <td>{{ $customer->timetable ?: '-' }}</td>
                                <td>Rp {{ number_format($customer->effectivePrice(), 0, ',', '.') }}</td>
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

@include('transactions._modal-form', [
    'modalId' => 'dashboardTransactionModal',
    'formId' => 'quickTransactionForm',
    'customerSelectId' => 'dashboardCustomer',
    'priceInputId' => 'dashboardPrice',
    'timeInputId' => 'dashboardTransactionTime',
    'useServerId' => 'dashboardUseServerTime',
])
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
                priceInput.value = window.SelowaMoney ? window.SelowaMoney.format(price) : price;
            }
        }

        customerSelect.on('change', syncPriceFromSelected);

        $('#dashboardCustomerTable tbody').on('click', 'tr.customer-picker-row', function () {
            customerSelect.val(this.dataset.customerId).trigger('change');
            priceInput.value = window.SelowaMoney ? window.SelowaMoney.format(this.dataset.price || '') : (this.dataset.price || '');
            if (window.SelowaModal) {
                window.SelowaModal.show('#dashboardTransactionModal');
            } else {
                $('#dashboardTransactionModal').modal('show');
            }
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
