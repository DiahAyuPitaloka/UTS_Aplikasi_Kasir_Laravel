<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Riwayat Penjualan Aplikasi Kasir</h2>
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Kasir</th>
                <th>Total</th>
                <th>Dibayar</th>
                <th>Kembalian</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
            <tr>
                <td>{{ $sale->invoice_code }}</td>
                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</td>
                <td>{{ $sale->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>