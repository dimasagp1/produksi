<!DOCTYPE html>
<html>

<head>
    <title>Laporan Produksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 9pt;
            color: #333;
        }

        /* --- UTILITY --- */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .mb-1 {
            margin-bottom: 5px;
        }

        .mt-2 {
            margin-top: 10px;
        }

        /* --- TABLE STYLES --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px 5px;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background-color: #e5e7eb;
            /* Gray 200 */
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
        }

        td {
            font-size: 8pt;
        }

        /* --- HEADER / KOP SURAT --- */
        .company-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .company-address {
            font-size: 9pt;
            margin: 2px 0;
        }

        .report-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .report-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            color: #1e3a8a;
            /* Navy Blue */
        }

        .report-meta {
            font-size: 8pt;
            color: #555;
            margin-top: 3px;
        }

        /* --- SECTION TITLES --- */
        .section-title {
            background-color: #1f2937;
            /* Slate 800 */
            color: #fff;
            padding: 5px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 9pt;
        }

        .sub-title {
            background-color: #f3f4f6;
            /* Gray 100 */
            padding: 3px 5px;
            font-weight: bold;
            font-size: 8pt;
            margin-top: 8px;
            margin-bottom: 3px;
            border-left: 3px solid #3b82f6;
            /* Blue border */
        }

        /* --- PAGE BREAK --- */
        .page-break {
            page-break-before: always;
        }

        /* --- SIGNATURE TABLE (NO BORDER) --- */
        .signature-table,
        .signature-table th,
        .signature-table td {
            border: none;
        }

        .signature-table {
            margin-top: 40px;
        }
    </style>
</head>

<body>

    {{-- 1. KOP SURAT --}}
    <div class="company-header">
        <h1 class="company-name">PT. NAMA PERUSAHAAN ANDA</h1>
        <p class="company-address">Jl. Kawasan Industri No. 123, Semarang, Jawa Tengah - Indonesia</p>
        <p class="company-address">Telp: (024) 1234567 | Email: produksi@perusahaan.com</p>
    </div>

    {{-- 2. HEADER LAPORAN --}}
    <div class="report-header">
        <h2 class="report-title">LAPORAN DASHBOARD PRODUKSI</h2>
        <div class="report-meta">
            Periode: {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }} <br>
            Dicetak Oleh: {{ $userName }} | Tanggal Cetak: {{ date('d-m-Y H:i') }}
        </div>
    </div>

    {{-- 3. RINGKASAN KPI --}}
    <div class="section-title">RINGKASAN KPI</div>
    <table>
        <thead>
            <tr>
                <th style="background-color: #dbeafe;">Target Produksi</th>
                <th style="background-color: #fef3c7;">Achievement</th>
                <th style="background-color: #dbeafe;">Total Output</th>
                <th style="background-color: #fee2e2;">Total Reject</th>
                <th style="background-color: #f3e8ff;">Avg Yield</th>
                <th style="background-color: #ccfbf1;">Efisiensi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center font-bold">{{ number_format($totalTarget, 0, ',', '.') }}</td>
                <td class="text-center font-bold">{{ number_format($achievement, 2) }}%</td>
                <td class="text-center font-bold">{{ number_format($totalOutput, 0, ',', '.') }}</td>
                <td class="text-center font-bold">{{ number_format($totalReject, 0, ',', '.') }}</td>
                <td class="text-center font-bold">{{ number_format($avgYield, 2) }}%</td>
                <td class="text-center font-bold">{{ number_format($avgEfficiency, 2) }}%</td>
            </tr>
        </tbody>
    </table>

    {{-- A. TREN OUTPUT HARIAN --}}
    <div class="section-title">A. TREN PRODUKSI HARIAN</div>
    <table>
        <thead>
            <tr>
                <th width="40%">Tanggal</th>
                <th width="30%">Target</th>
                <th width="30%">Total Output</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trendOutput as $t)
                {{-- PERBAIKAN: Menggunakan array syntax $t['key'] --}}
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($t['date'])->format('d M Y') }}</td>
                    <td class="text-right">{{ number_format($t['total_target'], 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($t['total'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- B. DETAIL DATA PRODUKSI --}}
    <div class="section-title">B. DETAIL DATA PRODUKSI PER PRODUK</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Produk / Tanggal</th>
                <th width="10%">QTY Theory</th>
                <th width="10%">Total Output</th>
                <th width="10%">QTY Good</th>
                <th width="10%">QTY Reject</th>
                <th width="10%">Theo Yield</th>
                <th width="10%">Act Yield</th>
                <th width="10%">Reject %</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($trendTableData as $productName => $rows)
                @php
                    $sumTheory = $rows->sum('sum_theory');
                    $sumOutput = $rows->sum('sum_output');
                    $sumGood = $rows->sum('sum_good');
                    $sumReject = $rows->sum('sum_reject');
                    $avgTheoYield = $sumTheory > 0 ? ($sumOutput / $sumTheory) * 100 : 0;
                    $avgActYield = $sumOutput > 0 ? ($sumGood / $sumOutput) * 100 : 0;
                    $avgRejectPct = $sumOutput > 0 ? ($sumReject / $sumOutput) * 100 : 0;
                @endphp
                <tr style="background-color: #f9fafb; font-weight: bold;">
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $productName }} ({{ $rows->count() }} Hari)</td>
                    <td class="text-right">{{ number_format($sumTheory, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($sumOutput, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($sumGood, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($sumReject, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($avgTheoYield, 2) }}%</td>
                    <td class="text-center">{{ number_format($avgActYield, 2) }}%</td>
                    <td class="text-center">{{ number_format($avgRejectPct, 2) }}%</td>
                </tr>
                {{-- Child Rows --}}
                @foreach ($rows as $row)
                    @php
                        $d_theoYield = $row->sum_theory > 0 ? ($row->sum_output / $row->sum_theory) * 100 : 0;
                        $d_actYield = $row->sum_output > 0 ? ($row->sum_good / $row->sum_output) * 100 : 0;
                        $d_rejectPct = $row->sum_output > 0 ? ($row->sum_reject / $row->sum_output) * 100 : 0;
                    @endphp
                    <tr>
                        <td></td>
                        <td style="padding-left: 15px;">{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                        <td class="text-right">{{ number_format($row->sum_theory, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($row->sum_output, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($row->sum_good, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($row->sum_reject, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($d_theoYield, 2) }}%</td>
                        <td class="text-center">{{ number_format($d_actYield, 2) }}%</td>
                        <td class="text-center">{{ number_format($d_rejectPct, 2) }}%</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    @if ($isFullAccess)

        <div class="page-break"></div>

        {{-- C. TOP 10 MESIN --}}
        <div class="section-title">C. 10 MESIN & PRODUK TERATAS</div>
        <table>
            <thead>
                <tr>
                    <th width="30%">Mesin</th>
                    <th width="45%">Produk</th>
                    <th width="25%">Total Output</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topMachineProducts as $mp)
                    <tr>
                        <td>{{ $mp->machine_name }}</td>
                        <td>{{ $mp->product_name }}</td>
                        <td class="text-right">{{ number_format($mp->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- D. TOP REJECT BY MACHINE --}}
        <div class="section-title">D. TOTAL REJECT BERDASARKAN MESIN</div>
        <table>
            <thead>
                <tr>
                    <th width="70%">Mesin</th>
                    <th width="30%">Total Reject</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rejectByMachine as $rm)
                    <tr>
                        <td>{{ $rm->label }}</td>
                        <td class="text-right">{{ number_format($rm->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- E. OPERATOR PERFORMANCE --}}
        <div class="section-title">E. 10 PERINGKAT KINERJA OPERATOR TERATAS</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">Rank</th>
                    <th width="20%">Nama Operator</th>
                    <th width="15%">Target</th>
                    <th width="15%">Output</th>
                    <th width="15%">Finish Good</th>
                    <th width="15%">Output Rate</th>
                    <th width="15%">FG Rate</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($operatorPerformance as $index => $op)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $op->operator_name }}</td>
                        <td class="text-right">{{ number_format($op->total_target, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($op->actual_output, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($op->actual_fg, 0, ',', '.') }}</td>
                        <td class="text-center font-bold">{{ number_format($op->output_rate, 2) }}%</td>
                        <td class="text-center font-bold">{{ number_format($op->fg_rate, 2) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="page-break"></div>

        {{-- F. TOP 10 REJECT --}}
        <div class="section-title">F. 10 ISU KUALITAS TERATAS</div>
        <table>
            <thead>
                <tr>
                    <th width="30%">Produk</th>
                    <th width="20%">Jenis Reject</th>
                    <th width="20%">Mesin</th>
                    <th width="15%">Qty</th>
                    <th width="15%">Kontribusi (%)</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotalReject = $totalReject > 0 ? $totalReject : 1; @endphp
                @forelse ($topRejects as $r)
                    @php $pct = ($r->total_qty / $grandTotalReject) * 100; @endphp
                    <tr>
                        <td>{{ $r->product_name }}</td>
                        <td>{{ $r->reject_name }}</td>
                        <td>{{ $r->machine_name }}</td>
                        <td class="text-right">{{ number_format($r->total_qty, 0, ',', '.') }}</td>
                        <td class="text-center font-bold">{{ number_format($pct, 2) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
            @if ($topRejects->isNotEmpty())
                <tfoot>
                    <tr style="background-color: #f3f4f6;">
                        <td colspan="3" class="text-right font-bold">TOTAL (Top 10)</td>
                        <td class="text-right font-bold">{{ number_format($totalTopRejectsQty, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>

        {{-- G. TOP 10 DOWNTIME --}}
        <div class="section-title">G. 10 DOWNTIME TERATAS</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Mesin</th>
                    <th width="40%">Jenis Downtime</th>
                    <th width="20%">Durasi (Menit)</th>
                    <th width="15%">Durasi (Jam)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topDowntimes as $dt)
                    <tr>
                        <td>{{ $dt->machine_name }}</td>
                        <td>{{ $dt->downtime_reason }}</td>
                        <td class="text-right">{{ number_format($dt->total_minutes, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($dt->total_minutes / 60, 1) }} Jam</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
            @if ($topDowntimes->isNotEmpty())
                <tfoot>
                    <tr style="background-color: #f3f4f6;">
                        <td colspan="2" class="text-right font-bold">TOTAL DURASI</td>
                        <td class="text-right font-bold">{{ number_format($totalTopDowntimeMinutes, 0, ',', '.') }}
                        </td>
                        <td class="text-center font-bold">{{ number_format($totalTopDowntimeMinutes / 60, 1) }} Jam
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <div class="page-break"></div>

        {{-- H. ANALISIS DISTRIBUSI --}}
        <div class="section-title">H. ANALISIS DISTRIBUSI PRODUK REJECT</div>

        @php
            $subTables = [
                ['title' => '1. Berdasarkan Jenis Reject', 'data' => $rejectByRejectItem],
                ['title' => '2. Berdasarkan Mesin', 'data' => $rejectByMachine],
                ['title' => '3. Berdasarkan Operator', 'data' => $rejectByOperator],
                ['title' => '4. Berdasarkan Koordinator', 'data' => $rejectByCoordinator],
                ['title' => '5. Berdasarkan Shift', 'data' => $rejectByShift],
            ];
        @endphp

        @foreach ($subTables as $table)
            <div class="sub-title">{{ $table['title'] }}</div>
            <table style="width: 60%; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th width="70%">Kategori</th>
                        <th width="30%">Total Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($table['data'] as $r)
                        <tr>
                            <td>{{ $r->label }}</td>
                            <td class="text-right">{{ number_format($r->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endforeach

        {{-- I. DETAIL REJECT TERATAS --}}
        <div class="section-title">I. DETAIL DATA REJECT TERATAS</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Tanggal</th>
                    <th width="8%">Shift</th>
                    <th width="10%">Batch</th>
                    <th width="15%">Produk</th>
                    <th width="12%">Jenis Reject</th>
                    <th width="10%">Mesin</th>
                    <th width="12%">Operator</th>
                    <th width="10%">Qty</th>
                    <th width="8%">%</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($detailRejects as $index => $d)
                    @php
                        $percentage = $totalReject > 0 ? ($d->qty / $totalReject) * 100 : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($d->production_date)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $d->shift_name }}</td>
                        <td class="text-center">{{ $d->batch_name }}</td>
                        <td>{{ $d->product_name }}</td>
                        <td>{{ $d->reject_name }}</td>
                        <td>{{ $d->machine_name }}</td>
                        <td>
                            {{ $d->operator_name }}<br>
                            <span style="font-size:7pt; color:#666;">({{ $d->coordinator_name }})</span>
                        </td>
                        <td class="text-right font-bold">{{ number_format($d->qty, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">{{ number_format($percentage, 2) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data detail</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <div style="margin-top:50px; text-align:center; color:#888; border:1px dashed #ccc; padding:20px;">
            <strong>Data Detail Dibatasi</strong><br>
            <span style="font-size:8pt;">Hanya tersedia untuk role: Admin, Manager, Super Admin</span>
        </div>
    @endif

    {{-- 4. FOOTER TANDA TANGAN --}}
    <table class="signature-table">
        <tr>
            <td colspan="3" class="text-right" style="padding-bottom: 20px;">
                Semarang, {{ date('d F Y') }}
            </td>
        </tr>
        <tr>
            <td width="33%" class="text-center">Dibuat Oleh,</td>
            <td width="33%" class="text-center">Diketahui Oleh,</td>
            <td width="33%" class="text-center">Disetujui Oleh,</td>
        </tr>
        <tr>
            <td height="60"></td>
            <td height="60"></td>
            <td height="60"></td>
        </tr>
        <tr>
            <td class="text-center font-bold" style="text-decoration: underline;">{{ $userName }}</td>
            <td class="text-center font-bold">( ........................... )</td>
            <td class="text-center font-bold">( ........................... )</td>
        </tr>
        <tr>
            <td class="text-center" style="font-size: 8pt;">Staff Admin</td>
            <td class="text-center" style="font-size: 8pt;">Manager Produksi</td>
            <td class="text-center" style="font-size: 8pt;">Plant Manager</td>
        </tr>
    </table>

</body>

</html>
