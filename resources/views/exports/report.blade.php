<!DOCTYPE html>
<html>

<head>
    <title>Laporan Harian Produksi</title>
    <style>
        /* Margin halaman disesuaikan untuk Header & Footer */
        @page {
            margin: 100px 15px 40px 15px;
        }

        body {
            font-family: sans-serif;
            font-size: 6px;
        }

        /* HEADER (KOP SURAT) */
        header {
            position: fixed;
            top: -85px;
            left: 0;
            right: 0;
            height: 80px;
            border-bottom: 2px double #000;
        }

        /* FOOTER */
        footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 8px;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        /* Tabel Kop */
        .kop-table {
            width: 100%;
            border: none;
            margin-bottom: 5px;
        }

        .kop-table td {
            border: none;
            padding: 2px;
            vertical-align: middle;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a202c;
        }

        .company-address {
            font-size: 9px;
            color: #4a5568;
        }

        /* Tabel Data Utama */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .data-table th,
        .data-table td {
            border: 0.5px solid #000;
            padding: 2px 3px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #e2e8f0;
            text-align: center;
            font-weight: bold;
            height: 15px;
            font-size: 6px;
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-red {
            color: red;
            font-weight: bold;
        }

        .bg-reject {
            background-color: #fce4e4;
        }

        .bg-downtime {
            background-color: #fff9c4;
        }

        .page-number:after {
            content: counter(page);
        }
    </style>
</head>

<body>
    {{-- HITUNG JUMLAH KOLOM DINAMIS --}}
    @php
        $maxRejects = $reports->map(fn($r) => $r->rejects->count())->max();
        $maxRejects = $maxRejects > 0 ? $maxRejects : 1;

        $maxDowntimes = $reports->map(fn($r) => $r->downtimes->count())->max();
        $maxDowntimes = $maxDowntimes > 0 ? $maxDowntimes : 1;
    @endphp

    {{-- KOP SURAT --}}
    <header>
        <table class="kop-table">
            <tr>
                <td width="15%" class="text-center">
                    <h1 style="font-size: 24px; color: #2563eb; margin:0;">EK</h1>
                </td>
                <td width="85%">
                    <div class="company-name">EK DEV PRODUCTION SYSTEM</div>
                    <div class="company-address">Jl. Industri Raya No. 123, Kawasan Industri, Indonesia</div>
                    <div class="company-address">Telp: (021) 555-1234 | Email: production@ekdev.com</div>
                </td>
            </tr>
        </table>
    </header>

    {{-- FOOTER --}}
    <footer>
        <table width="100%">
            <tr>
                <td style="border:none; text-align:left;">Dicetak oleh: {{ Auth::user()->name ?? 'System' }}</td>
                <td style="border:none; text-align:center;">Tgl Cetak: {{ now()->format('d/m/Y H:i') }}</td>
                <td style="border:none; text-align:right;">Hal <span class="page-number"></span></td>
            </tr>
        </table>
    </footer>

    {{-- KONTEN UTAMA --}}
    <main>
        <h3 style="text-align: center; font-size: 12px; margin: 0 0 5px 0; text-transform: uppercase;">Laporan Harian
            Produksi</h3>
        <p style="text-align: center; font-size: 8px; margin-bottom: 10px;">
            Periode: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : '-' }}
            s/d
            {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : '-' }}
        </p>

        <table class="data-table">
            <thead>
                <tr>
                    {{-- 1. IDENTITAS (Width disesuaikan agar muat di A4 Landscape) --}}
                    <th width="15">No</th>
                    <th width="30">Tgl</th>
                    <th width="20">Shift</th>
                    <th width="50">Batch</th>
                    <th width="30">Prioritas</th>
                    <th width="60">Produk</th>
                    <th width="25">Warna</th>
                    <th width="30">Mesin</th>
                    <th width="30">Op</th>
                    <th width="30">Coord</th>

                    {{-- 2. WAKTU --}}
                    <th width="25">Target</th>
                    <th width="30">Jam</th>
                    <th width="20">Mnt</th>
                    <th width="15">CT</th>
                    <th width="15">Cav</th>

                    {{-- 3. OUTPUT --}}
                    <th width="20">Theory</th>
                    <th width="20">Good</th>
                    <th width="20" class="text-red">NG</th>
                    <th width="20">Out</th>

                    {{-- 4. LOSSES --}}
                    <th width="20">Purg</th>
                    <th width="20">WIP</th>
                    <th width="20">Cnt</th>

                    {{-- 5. KPI --}}
                    <th width="20">Eff</th>
                    <th width="20">Yld</th>

                    {{-- DINAMIS HEADER REJECT --}}
                    @for ($i = 1; $i <= $maxRejects; $i++)
                        <th width="40" style="background-color: #fce4e4;">D.Reject {{ $i }}</th>
                    @endfor

                    {{-- DINAMIS HEADER DOWNTIME --}}
                    @for ($i = 1; $i <= $maxDowntimes; $i++)
                        <th width="45" style="background-color: #fff9c4;">D.Downtime {{ $i }}</th>
                    @endfor

                    <th width="30">Pack</th>
                    <th width="40">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $r)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($r->production_date)->format('d/m') }}</td>
                        <td class="text-center">{{ $r->shift->name ?? '-' }}</td>
                        <td class="text-left">{{ $r->batch->batch_code ?? '-' }}</td>
                        <td class="text-center">{{ ucfirst($r->batch->priority ?? '-') }}</td>
                        <td class="text-left" style="font-size: 5px;">
                            {{ Str::limit($r->batch->product->name ?? '-', 25) }}</td>
                        <td class="text-center">{{ Str::limit($r->batch->color->name ?? '-', 8) }}</td>
                        <td class="text-center">{{ Str::limit($r->machine->name ?? '-', 10) }}</td>
                        <td class="text-center">{{ Str::limit($r->operator->name ?? '-', 8) }}</td>
                        <td class="text-center">{{ Str::limit($r->coordinator->name ?? '-', 8) }}</td>

                        <td class="text-right">{{ $r->batch->target_quantity }}</td>
                        <td class="text-center" style="font-size: 5px;">
                            {{ \Carbon\Carbon::parse($r->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($r->end_time)->format('H:i') }}
                        </td>
                        <td class="text-center">{{ $r->total_minutes }}</td>
                        <td class="text-center">{{ $r->cycle_time }}</td>
                        <td class="text-center">{{ $r->cavity }}</td>

                        <td class="text-right">{{ $r->qty_theory }}</td>
                        <td class="text-right font-bold">{{ $r->qty_good }}</td>
                        <td class="text-right text-red">{{ $r->qty_reject_total }}</td>
                        <td class="text-right">{{ $r->total_output }}</td>

                        <td class="text-right">{{ $r->qty_purging }}</td>
                        <td class="text-right">{{ $r->wip }}</td>
                        <td class="text-right">{{ $r->total_counter }}</td>

                        <td class="text-center {{ $r->efficiency < 90 ? 'text-red' : '' }}">
                            {{ round($r->efficiency, 1) }}%</td>
                        <td class="text-center">{{ round($r->yield, 1) }}%</td>

                        {{-- ISI DINAMIS REJECT --}}
                        @foreach ($r->rejects as $rej)
                            <td class="text-left bg-reject" style="font-size: 5px; white-space: normal;">
                                {{ Str::limit($rej->rejectItem->name ?? '-', 10) }}: {{ $rej->qty }}
                            </td>
                        @endforeach
                        {{-- Sisa Kolom Kosong Reject --}}
                        @for ($j = $r->rejects->count(); $j < $maxRejects; $j++)
                            <td class="bg-reject"></td>
                        @endfor

                        {{-- ISI DINAMIS DOWNTIME --}}
                        @foreach ($r->downtimes as $dt)
                            <td class="text-left bg-downtime" style="font-size: 5px; white-space: normal;">
                                {{ Str::limit($dt->downtime->name ?? '-', 10) }}: {{ $dt->duration }}m
                            </td>
                        @endforeach
                        {{-- Sisa Kolom Kosong Downtime --}}
                        @for ($k = $r->downtimes->count(); $k < $maxDowntimes; $k++)
                            <td class="bg-downtime"></td>
                        @endfor

                        <td class="text-center">{{ $r->packaging_qty }}</td>
                        <td class="text-left" style="font-size: 5px; white-space: normal;">
                            {{ Str::limit($r->notes, 30) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>

</html>
