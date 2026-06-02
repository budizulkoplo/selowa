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
        .total-row td { font-weight: bold; background: #f3f4f6; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <p>Periode: {{ $period }}</p>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Mobil</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->transaction_code }}</td>
                    <td>{{ $transaction->customer?->name ?: $transaction->customer_name_snapshot ?: '-' }}</td>
                    <td>{{ $transaction->deliveryRun?->label() ?: '-' }}</td>
                    <td class="text-right">{{ $transaction->qty }}</td>
                    <td class="text-right">{{ $transaction->price }}</td>
                    <td class="text-right">{{ $transaction->total() }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Tidak ada data.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="6" class="text-right">Total</td>
                <td class="text-right">{{ $total }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
