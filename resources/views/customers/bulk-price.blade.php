@extends('layouts.app')

@section('title', 'Update Harga Pelanggan')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')

    <div class="ibox">
        <div class="ibox-title"><h5>Update Harga Pelanggan Serentak</h5></div>
        <div class="ibox-content">
            <form method="POST" action="{{ route('customers.bulk-price.update') }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Target Update</label>
                        <select name="mode" id="bulkPriceMode" class="form-control">
                            <option value="all">Semua pelanggan aktif</option>
                            <option value="price">Pelanggan dengan harga tertentu</option>
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
                    <div class="col-md-4 form-group">
                        <label>Harga Baru</label>
                        <input name="new_price" type="number" min="0" class="form-control" required>
                    </div>
                </div>
                <a href="{{ route('customers.index') }}" class="btn btn-white">Kembali</a>
                <button class="btn btn-warning" onclick="return confirm('Update harga pelanggan sekarang?')"><i class="fa fa-refresh"></i> Update Harga</button>
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

        if (!mode || !oldPriceGroup) {
            return;
        }

        const sync = () => {
            oldPriceGroup.style.display = mode.value === 'price' ? '' : 'none';
        };

        mode.addEventListener('change', sync);
        sync();
    }());
</script>
@endsection
