<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Nota Servis #{{ $tx->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .container {
            width: 95%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            margin: 0;
        }

        .info {
            margin-bottom: 20px;
        }

        .info .left {
            float: left;
            width: 48%;
        }

        .info .right {
            float: right;
            width: 48%;
            text-align: right;
        }

        .info::after {
            content: "";
            display: table;
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .total {
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>NOTA SERVIS</h1>
            <p>{{ config('app.name', 'Bengkel POS') }}</p>
            <p>Jl. Urip Sumoharjo, Pekalongan (Ganti dengan alamat Anda)</p>
        </div>

        <div class="info">
            <div class="left">
                <strong>No. Transaksi:</strong> #{{ $tx->id }}<br>
                <strong>Pelanggan:</strong> {{ $tx->customer->name ?? 'N/A' }}<br>
                <strong>No. HP:</strong> {{ $tx->customer->phone ?? 'N/A' }}
            </div>
            <div class="right">
                <strong>Tanggal:</strong> {{ $tx->service_date }}<br>
                <strong>Mekanik:</strong> {{ $tx->mechanic->name ?? 'N/A' }}<br>
                <strong>Kendaraan:</strong> {{ $tx->vehicle->brand ?? 'N/A' }} {{ $tx->vehicle->model ?? 'N/A' }}<br>
                <strong>Nomor Polisi:</strong> {{ $tx->vehicle->license_plate ?? 'N/A' }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Kategori</th>
                    <th>Qty</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                    <tr>
                        <td>{{ $detail->itemable->name ?? 'Item Dihapus' }}</td>
                        <td>{{ $detail->itemable_type == 'App\Models\Service' ? 'Jasa' : 'Sparepart' }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>Rp {{ number_format($detail->price_at_transaction) }}</td>
                        <td>Rp {{ number_format($detail->price_at_transaction * $detail->quantity) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="total">GRAND TOTAL</td>
                    <td class="total">Rp {{ number_format($tx->total_price) }}</td>
                </tr>
            </tfoot>
        </table>

        @if ($tx->notes)
            <div style="margin-bottom: 20px; border: 1px dashed #ccc; padding: 10px;">
                <strong>Catatan:</strong><br>
                {{ $tx->notes }}
            </div>
        @endif

        <p style="text-align: center;">Terima kasih telah servis di bengkel kami!</p>
    </div>
</body>

</html>
