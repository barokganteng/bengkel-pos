<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan & Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2 class="text-center">Laporan Keuangan & Transaksi</h2>
    <p>Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kendaraan</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $totalRevenue = 0; @endphp
            @foreach($transactions as $tx)
                @php 
                    if($tx->status === 'paid') {
                        $totalRevenue += $tx->total_price;
                    }
                @endphp
                <tr>
                    <td>#{{ $tx->id }}</td>
                    <td>{{ $tx->service_date ? \Carbon\Carbon::parse($tx->service_date)->format('d M Y H:i') : '-' }}</td>
                    <td>{{ $tx->customer->name ?? 'N/A' }}</td>
                    <td>{{ $tx->vehicle->license_plate ?? 'N/A' }}</td>
                    <td>
                        @if($tx->status == 'pending')
                            Antrean
                        @elseif($tx->status == 'in_progress')
                            Pengerjaan
                        @elseif($tx->status == 'done')
                            Pembayaran
                        @elseif($tx->status == 'paid')
                            Selesai
                        @else
                            {{ ucfirst(str_replace('_', ' ', $tx->status)) }}
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">Total Pendapatan (Status Selesai)</th>
                <th class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
