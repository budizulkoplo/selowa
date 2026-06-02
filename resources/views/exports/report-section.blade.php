<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111827; }
        h2, p { margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px; }
        th { background: #e5eef8; text-align: left; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <p>Periode: {{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }}</p>

    @if ($section === 'daily')
        <table>
            <thead><tr><th>Tanggal</th><th class="text-right">Transaksi</th><th class="text-right">Galon</th><th class="text-right">Pendapatan</th></tr></thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr><td>{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td><td class="text-right">{{ $row->transaction_count }}</td><td class="text-right">{{ $row->qty_total }}</td><td class="text-right">{{ $row->income_total }}</td></tr>
                @empty
                    <tr><td colspan="4">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($section === 'customers')
        <table>
            <thead><tr><th>Pelanggan</th><th>Telp</th><th class="text-right">Galon</th><th class="text-right">Pendapatan</th></tr></thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr><td>{{ $row->name }}</td><td>{{ $row->phone ?: '-' }}</td><td class="text-right">{{ $row->qty_total }}</td><td class="text-right">{{ $row->income_total }}</td></tr>
                @empty
                    <tr><td colspan="4">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($section === 'vehicles')
        <table>
            <thead><tr><th>Tanggal</th><th>Mobil</th><th>Driver</th><th>Area</th><th class="text-right">Transaksi</th><th class="text-right">Galon</th></tr></thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr><td>{{ $row->run_date?->format('d/m/Y') }}</td><td>{{ $row->vehicle?->name ?: '-' }}</td><td>{{ $row->driver_name ?: $row->vehicle?->driver_name ?: '-' }}</td><td>{{ $row->area ?: '-' }}</td><td class="text-right">{{ $row->transactions_count }}</td><td class="text-right">{{ $row->qty_total ?: 0 }}</td></tr>
                @empty
                    <tr><td colspan="6">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table>
            <thead><tr><th>Nama</th><th class="text-right">Stok</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($rows as $gallon)
                    <tr><td>{{ $gallon->name }}</td><td class="text-right">{{ $gallon->stock }}</td><td>{{ $gallon->is_active ? 'Aktif' : 'Nonaktif' }}</td></tr>
                @empty
                    <tr><td colspan="3">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif
</body>
</html>
