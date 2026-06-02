@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')

    <div class="dashboard-banner">
        <div>
            <span class="dashboard-kicker">Selowa Backoffice</span>
            <h2>Operasional isi ulang air minum</h2>
            <p>{{ now()->translatedFormat('l, d F Y') }} - pantau pelanggan, transaksi, pendapatan, dan rute pengiriman dari satu layar.</p>
        </div>
        <div class="dashboard-banner-actions">
            <button type="button" class="btn btn-primary" data-selowa-modal="#dashboardTransactionModal"><i class="fa fa-plus"></i> Input Transaksi</button>
            <a href="{{ route('reports.index') }}" class="btn btn-white"><i class="fa fa-bar-chart"></i> Laporan</a>
        </div>
    </div>

    <div class="row">
        @foreach ($summaryCards as $index => $card)
            <div class="col-lg-3 col-md-6">
                <div class="ibox dashboard-card dashboard-card-{{ $index + 1 }}">
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
            <h5>Data Pelanggan</h5>
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
    .dashboard-banner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
        padding: 22px 24px;
        color: #ffffff;
        background: linear-gradient(135deg, #1167a8 0%, #15958b 58%, #2f4050 100%);
        border-radius: 6px;
        box-shadow: 0 10px 24px rgba(47, 64, 80, .16);
    }

    .dashboard-banner h2 {
        margin: 4px 0 5px;
        color: #ffffff;
        font-size: 24px;
        font-weight: 700;
    }

    .dashboard-banner p {
        margin: 0;
        color: rgba(255,255,255,.88);
    }

    .dashboard-kicker {
        display: inline-block;
        color: rgba(255,255,255,.78);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .dashboard-banner-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .dashboard-card .ibox-content {
        border-top: 0;
        border-left: 4px solid #1ab394;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .06);
    }

    .dashboard-card-1 .ibox-content { border-left-color: #1677b9; }
    .dashboard-card-2 .ibox-content { border-left-color: #0f9aa8; }
    .dashboard-card-3 .ibox-content { border-left-color: #1f9d66; }
    .dashboard-card-4 .ibox-content { border-left-color: #d98b13; }

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

    .customer-picker-row:hover {
        background: #eef7f6 !important;
    }

    @media (max-width: 768px) {
        .dashboard-banner {
            display: block;
            padding: 18px;
        }

        .dashboard-banner-actions {
            justify-content: flex-start;
            margin-top: 14px;
        }
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
