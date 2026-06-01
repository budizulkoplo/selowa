@extends('layouts.app')

@section('title', 'Company')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title"><h5>Konfigurasi Perusahaan</h5></div>
        <div class="ibox-content">
            <form method="POST" action="{{ route('company.update') }}">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nama Perusahaan</label>
                        <input name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Telepon</label>
                        <input name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Email</label>
                        <input name="email" type="email" class="form-control" value="{{ old('email', $company->email) }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="address" id="companyAddress" class="form-control" rows="3">{{ old('address', $company->address) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Embed Map</label>
                    <textarea name="embed_map" id="companyEmbedMap" class="form-control" rows="3">{{ old('embed_map', $company->embed_map) }}</textarea>
                    <div class="m-t-sm">
                        <button type="button" class="btn btn-white btn-sm" id="openMapSearch"><i class="fa fa-map-marker"></i> Cari di Google Maps</button>
                        <button type="button" class="btn btn-white btn-sm" id="useDeviceLocation"><i class="fa fa-location-arrow"></i> Gunakan Lokasi Perangkat</button>
                        <button type="button" class="btn btn-white btn-sm" id="previewMap"><i class="fa fa-eye"></i> Preview Map</button>
                    </div>
                    <div class="map-preview m-t" id="mapPreviewWrap" style="display:none">
                        <iframe id="mapPreview" width="100%" height="260" style="border:0" loading="lazy"></iframe>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nama File Logo/Gambar</label>
                    <input name="image" class="form-control" value="{{ old('image', $company->image) }}">
                </div>
                <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    (function () {
        const address = document.getElementById('companyAddress');
        const embedMap = document.getElementById('companyEmbedMap');
        const previewWrap = document.getElementById('mapPreviewWrap');
        const preview = document.getElementById('mapPreview');

        function extractMapUrl(value) {
            const match = value.match(/src=["']([^"']+)["']/i);
            return match ? match[1] : value;
        }

        function showPreview() {
            const url = extractMapUrl((embedMap.value || '').trim());
            if (!url) {
                return;
            }

            preview.src = url;
            previewWrap.style.display = 'block';
        }

        document.getElementById('openMapSearch').addEventListener('click', () => {
            const query = encodeURIComponent(address.value || 'Selowa');
            window.open('https://www.google.com/maps/search/?api=1&query=' + query, '_blank');
        });

        document.getElementById('previewMap').addEventListener('click', showPreview);

        document.getElementById('useDeviceLocation').addEventListener('click', () => {
            if (!navigator.geolocation) {
                alert('Browser belum mendukung lokasi perangkat.');
                return;
            }

            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude.toFixed(7);
                const lng = position.coords.longitude.toFixed(7);
                embedMap.value = `https://maps.google.com/maps?q=${lat},${lng}&z=16&output=embed`;
                if (!address.value) {
                    address.value = `Koordinat ${lat}, ${lng}`;
                }
                showPreview();
            });
        });

        if ((embedMap.value || '').trim()) {
            showPreview();
        }
    }());
</script>
@endsection
