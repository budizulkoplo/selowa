@extends('layouts.app')

@section('title', 'Update Harga Pelanggan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')

    <div class="ibox">
        <div class="ibox-title">
            <h5>Update Harga Pelanggan Serentak</h5>
            <div class="ibox-tools">
                <button type="button" class="btn btn-warning btn-xs" data-selowa-modal="#bulkPriceModal">
                    <i class="fa fa-refresh"></i> Update Harga
                </button>
            </div>
        </div>
        <div class="ibox-content">
            <p class="text-muted">Gunakan tombol update untuk mengubah harga semua pelanggan aktif, pelanggan pada desa tertentu, atau pelanggan dengan harga lama tertentu.</p>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Harga Saat Ini</th></tr></thead>
                    <tbody>
                        @foreach ($currentPrices as $price)
                            <tr><td>Rp {{ number_format($price, 0, ',', '.') }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <a href="{{ route('customers.index') }}" class="btn btn-white">Kembali</a>
        </div>
    </div>
</div>

<div class="modal fade" id="bulkPriceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('customers.bulk-price.update') }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Update Harga Serentak</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Target Update</label>
                            <select name="mode" id="bulkPriceMode" class="form-control">
                                <option value="all">Semua pelanggan aktif</option>
                                <option value="village">Berdasarkan desa</option>
                                <option value="price">Berdasarkan harga lama</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group" id="villageGroup">
                            <label>Desa</label>
                            <select name="village_id" class="form-control">
                                <option value="">Pilih desa</option>
                                @foreach ($villages as $village)
                                    <option value="{{ $village->id }}">{{ $village->name }} - {{ $village->district?->name }} - {{ $village->district?->city?->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group" id="oldPriceGroup">
                            <label>Harga Lama</label>
                            <select name="old_price" class="form-control">
                                <option value="">Pilih harga lama</option>
                                @foreach ($currentPrices as $price)
                                    <option value="{{ $price }}">Rp {{ number_format($price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Harga Baru</label>
                            <input name="new_price" type="text" inputmode="numeric" class="form-control money-input" placeholder="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Batal</button>
                    <button class="btn btn-warning" onclick="return confirm('Update harga pelanggan sekarang?')"><i class="fa fa-refresh"></i> Update Harga</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    (function () {
        const mode = document.getElementById('bulkPriceMode');
        const oldPriceGroup = document.getElementById('oldPriceGroup');
        const villageGroup = document.getElementById('villageGroup');

        if (!mode || !oldPriceGroup || !villageGroup) {
            return;
        }

        const sync = () => {
            oldPriceGroup.style.display = mode.value === 'price' ? '' : 'none';
            villageGroup.style.display = mode.value === 'village' ? '' : 'none';
        };

        mode.addEventListener('change', sync);
        sync();
    }());
</script>
@endsection
