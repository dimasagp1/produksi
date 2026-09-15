<table>
    <thead>
        {{-- KOP SURAT --}}
        <tr>
            <th colspan="21"
                style="text-align: center; font-weight: bold; font-size: 16px; height: 30px; border: 1px solid #000000;">
                AEJ PRODUCTION APP
            </th>
        </tr>
        <tr>
            <th colspan="21" style="text-align: center; font-size: 11px; border: 1px solid #000000;">
                Jl. Industri Raya No. 123, Kawasan Industri, Indonesia | Telp: (021) 555-1234
            </th>
        </tr>
        <tr>
            <th colspan="21"
                style="text-align: center; font-weight: bold; font-size: 12px; height: 25px; vertical-align: middle; border: 1px solid #000000;">
                LAPORAN DATA BATCH PRODUKSI LENGKAP
            </th>
        </tr>
        <tr>
            <th colspan="21"
                style="text-align: center; font-size: 10px; font-style: italic; border: 1px solid #000000;">
                Filter Status: {{ request('status') ? ucfirst(request('status')) : 'Semua' }} |
                Dicetak: {{ now()->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
            </th>
        </tr>
        <tr>
            <th colspan="21" style="border: 1px solid #000000;"></th>
        </tr>

        {{-- HEADER TABEL DENGAN BORDER DAN WIDTH --}}
        <tr>
            <th width="5"
                style="font-weight: bold; background: #dce6f1; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                No</th>
            <th width="22"
                style="font-weight: bold; background: #dce6f1; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Kode Batch</th>
            <th width="15"
                style="font-weight: bold; background: #dce6f1; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Status</th>
            <th width="12"
                style="font-weight: bold; background: #dce6f1; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Prioritas</th>
            <th width="40"
                style="font-weight: bold; background: #dce6f1; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Nama Produk</th>
            <th width="15"
                style="font-weight: bold; background: #dce6f1; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Warna</th>
            <th width="25"
                style="font-weight: bold; background: #dce6f1; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Mesin</th>

            {{-- KOLOM METRIK --}}
            <th width="15"
                style="font-weight: bold; background: #fff2cc; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Batch Size</th>
            <th width="15"
                style="font-weight: bold; background: #e2efda; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Total Produksi</th>
            <th width="15"
                style="font-weight: bold; background: #e2efda; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                FG (Good)</th>
            <th width="12"
                style="font-weight: bold; background: #fce4d6; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Reject (Qty)</th>
            <th width="10"
                style="font-weight: bold; background: #fce4d6; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Reject (%)</th>
            <th width="15"
                style="font-weight: bold; background: #fff2cc; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Sisa Target</th>

            <th width="10"
                style="font-weight: bold; background: #ededed; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                WIP</th>
            <th width="12"
                style="font-weight: bold; background: #ededed; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Counter</th>
            <th width="10"
                style="font-weight: bold; background: #ddebf7; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Sample</th>
            <th width="12"
                style="font-weight: bold; background: #fff2cc; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Downtime (m)</th>
            <th width="12"
                style="font-weight: bold; background: #e2efda; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Avg Eff (%)</th>

            <th width="15"
                style="font-weight: bold; background: #dce6f1; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Tgl Mulai</th>
            <th width="15"
                style="font-weight: bold; background: #dce6f1; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Tgl Selesai</th>
            <th width="25"
                style="font-weight: bold; background: #e6e6fa; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                Terakhir Update</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($batches as $batch)
            @php
                $totalSample = $batch->reports->sum('qty_sample');
                $lastReport = $batch->reports->sortByDesc('created_at')->first();
                $wip = $lastReport ? $lastReport->wip : 0;
                $lastUpdateDate = $lastReport ? $lastReport->created_at : $batch->updated_at;

                if (in_array(strtolower($batch->status), ['completed', 'canceled'])) {
                    $wip = 0;
                }

                $counter = $lastReport ? $lastReport->total_counter : 0;
                $fg = $batch->reports->sum('qty_good');
                $totalDowntime = $batch->reports->sum('downtime_total');
                $avgEff = $batch->reports->avg('efficiency') ?? 0;

                $remaining = max(0, $batch->target_quantity - $batch->current_quantity);
                if (in_array(strtolower($batch->status), ['completed', 'canceled'])) {
                    $remaining = 0;
                }

                $totalProduction = $batch->current_quantity + $batch->reject_quantity;
                $rejectRate = $totalProduction > 0 ? ($batch->reject_quantity / $totalProduction) * 100 : 0;
            @endphp

            <tr>
                <td style="text-align: center; border: 1px solid #000000;">{{ $loop->iteration }}</td>
                <td style="text-align: left; border: 1px solid #000000;">{{ $batch->batch_code }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ ucfirst($batch->status) }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ ucfirst($batch->priority) }}</td>
                <td style="text-align: left; border: 1px solid #000000;">{{ $batch->product->name ?? '-' }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $batch->color->name ?? '-' }}</td>
                <td style="text-align: left; border: 1px solid #000000;">{{ $batch->machine->name ?? 'Belum Assign' }}
                </td>

                {{-- Data Angka --}}
                <td style="text-align: right; border: 1px solid #000000;">{{ $batch->target_quantity }}</td>
                <td style="text-align: right; border: 1px solid #000000;">{{ $totalProduction }}</td>
                <td style="text-align: right; color: #059669; font-weight: bold; border: 1px solid #000000;">
                    {{ $fg }}</td>
                <td style="text-align: right; color: #e11d48; border: 1px solid #000000;">{{ $batch->reject_quantity }}
                </td>
                <td style="text-align: center; color: #e11d48; border: 1px solid #000000;">
                    {{ number_format($rejectRate, 1) }}%</td>
                <td style="text-align: right; color: #b45309; font-weight: bold; border: 1px solid #000000;">
                    {{ $remaining }}</td>

                <td style="text-align: right; border: 1px solid #000000;">{{ $wip }}</td>
                <td style="text-align: right; color: #4f46e5; border: 1px solid #000000;">{{ $counter }}</td>
                <td style="text-align: right; color: #2563eb; border: 1px solid #000000;">{{ $totalSample }}</td>

                <td style="text-align: right; border: 1px solid #000000;">{{ $totalDowntime }}</td>
                <td style="text-align: center; font-weight: bold; border: 1px solid #000000;">
                    {{ number_format($avgEff, 1) }}%</td>

                <td style="text-align: center; border: 1px solid #000000;">
                    {{ \Carbon\Carbon::parse($batch->start_date)->format('d/m/Y') }}</td>
                <td style="text-align: center; border: 1px solid #000000;">
                    {{ $batch->deadline_date ? \Carbon\Carbon::parse($batch->deadline_date)->format('d/m/Y') : '-' }}
                </td>
                <td style="text-align: center; border: 1px solid #000000;">
                    {{ $lastUpdateDate ? \Carbon\Carbon::parse($lastUpdateDate)->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }}
                </td>
            </tr>
        @endforeach

        {{-- FOOTER --}}
        <tr>
            <td colspan="21" style="text-align: right; font-style: italic; border: 1px solid #000000;">
                Dicetak oleh: {{ Auth::user()->name ?? 'Admin' }}
            </td>
        </tr>
    </tbody>
</table>
