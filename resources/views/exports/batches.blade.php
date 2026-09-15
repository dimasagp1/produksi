<!DOCTYPE html>
<html>

<head>
    <title>Laporan Batch Produksi</title>
    <style>
        @page {
            margin: 20px 15px;
        }

        body {
            font-family: sans-serif;
            font-size: 7pt;
            /* Font kecil agar muat banyak kolom */
        }

        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 2px;
        }

        .header-sub {
            text-align: center;
            font-size: 9pt;
            margin-bottom: 5px;
        }

        .header-info {
            text-align: center;
            font-size: 8pt;
            font-style: italic;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 3px 2px;
            /* Padding tipis */
            vertical-align: middle;
        }

        th {
            background-color: #dce6f1;
            text-align: center;
            font-weight: bold;
            height: 25px;
        }

        /* Warna Header Kategori */
        th.head-target {
            background-color: #fff2cc;
        }

        /* Kuning */
        th.head-good {
            background-color: #e2efda;
        }

        /* Hijau */
        th.head-bad {
            background-color: #fce4d6;
        }

        /* Merah Muda */
        th.head-neutral {
            background-color: #ededed;
        }

        /* Abu */

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Warna Teks Data */
        .text-red {
            color: #dc2626;
        }

        .text-green {
            color: #059669;
        }

        .text-blue {
            color: #2563eb;
        }

        .text-amber {
            color: #d97706;
        }

        .text-indigo {
            color: #4f46e5;
        }
    </style>
</head>

<body>

    <div class="header-title">EK DEV PRODUCTION SYSTEM</div>
    <div class="header-sub">Jl. Industri Raya No. 123, Kawasan Industri, Indonesia | Telp: (021) 555-1234</div>
    <div class="header-title" style="font-size: 11pt; margin-top: 10px;">LAPORAN DATA BATCH PRODUKSI LENGKAP</div>
    <div class="header-info">
        Filter Status: {{ request('status') ? ucfirst(request('status')) : 'Semua' }} |
        Dicetak: {{ now()->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">Kode Batch</th>
                <th width="6%">Status</th>
                <th width="5%">Prio</th>
                <th width="12%">Produk</th>
                <th width="6%">Warna</th>
                <th width="7%">Mesin</th>

                {{-- METRIK --}}
                <th class="head-target" width="5%">Batch Size</th>
                <th class="head-target" width="5%">Out</th>
                <th class="head-target" width="5%">Sisa</th>

                <th class="head-good" width="5%">FG</th>

                <th class="head-bad" width="4%">Rej</th>
                <th class="head-bad" width="4%">Rej%</th>

                <th class="head-neutral" width="4%">WIP</th>
                <th class="head-neutral" width="4%">Ctr</th>
                <th style="background-color: #ddebf7;" width="4%">Smp</th>

                <th class="head-target" width="4%">DT(m)</th>
                <th class="head-good" width="4%">Eff</th>

                <th width="6%">Mulai</th>
                <th width="6%">Deadline</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($batches as $batch)
                @php
                    // HITUNG LOGIKA (Sama dengan Index & Excel)
                    $totalSample = $batch->reports->sum('qty_sample');
                    $lastReport = $batch->reports->sortByDesc('created_at')->first();
                    $wip = $lastReport ? $lastReport->wip : 0;
                    $counter = $lastReport ? $lastReport->total_counter : 0;
                    $fg = $batch->reports->sum('qty_good');
                    $totalDowntime = $batch->reports->sum('downtime_total');
                    $avgEff = $batch->reports->avg('efficiency') ?? 0;

                    $remaining = max(0, $batch->target_quantity - $batch->current_quantity);
                    $totalProduction = $batch->current_quantity + $batch->reject_quantity;
                    $rejectRate = $totalProduction > 0 ? ($batch->reject_quantity / $totalProduction) * 100 : 0;
                @endphp

                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-left font-bold">{{ $batch->batch_code }}</td>
                    <td class="text-center" style="text-transform: capitalize;">{{ $batch->status }}</td>
                    <td class="text-center" style="text-transform: capitalize;">{{ $batch->priority }}</td>
                    <td class="text-left">{{ $batch->product->name ?? '-' }}</td>
                    <td class="text-center">{{ $batch->color->name ?? '-' }}</td>
                    <td class="text-center">{{ $batch->machine->name ?? '-' }}</td>

                    {{-- Angka --}}
                    <td class="text-right">{{ number_format($batch->target_quantity, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($batch->current_quantity, 0, ',', '.') }}</td>
                    <td class="text-right text-amber font-bold">{{ number_format($remaining, 0, ',', '.') }}</td>

                    <td class="text-right text-green font-bold">{{ number_format($fg, 0, ',', '.') }}</td>

                    <td class="text-right text-red">{{ number_format($batch->reject_quantity, 0, ',', '.') }}</td>
                    <td class="text-center text-red">{{ number_format($rejectRate, 1) }}%</td>

                    <td class="text-right">{{ number_format($wip, 0, ',', '.') }}</td>
                    <td class="text-right text-indigo">{{ number_format($counter, 0, ',', '.') }}</td>
                    <td class="text-right text-blue">{{ number_format($totalSample, 0, ',', '.') }}</td>

                    <td class="text-right">{{ number_format($totalDowntime, 0, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ number_format($avgEff, 1) }}%</td>

                    <td class="text-center">{{ \Carbon\Carbon::parse($batch->start_date)->format('d/m/y') }}</td>
                    <td
                        class="text-center {{ $batch->deadline_date && \Carbon\Carbon::now()->gt($batch->deadline_date) && $batch->status != 'completed' ? 'text-red font-bold' : '' }}">
                        {{ $batch->deadline_date ? \Carbon\Carbon::parse($batch->deadline_date)->format('d/m/y') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: right; font-style: italic; font-size: 8pt;">
        Dicetak oleh: {{ Auth::user()->name ?? 'Admin' }}
    </div>

</body>

</html>
