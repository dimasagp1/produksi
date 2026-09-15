@php
    // Hitung total kolom: 26 (A-Z) + Total Master Reject + Total Master Downtime + 2 (Kemasan & Catatan)
    $totalCols = 26 + $masterRejects->count() + $masterDowntimes->count() + 2;
@endphp

<table>
    <thead>
        <tr>
            <th colspan="{{ $totalCols }}" style="font-weight: bold; font-size: 16px; text-align: center;">EK DEV
                PRODUCTION SYSTEM</th>
        </tr>
        <tr>
            <th colspan="{{ $totalCols }}" style="text-align: center;">Jl. Industri Raya No. 123, Kawasan Industri,
                Indonesia</th>
        </tr>
        <tr>
            <th colspan="{{ $totalCols }}" style="font-weight: bold; font-size: 12px; text-align: center;">LAPORAN
                HARIAN PRODUKSI</th>
        </tr>
        <tr>
            <th colspan="{{ $totalCols }}" style="font-style: italic; text-align: center;">Periode:
                {{ request('start_date') }} s/d {{ request('end_date') }}</th>
        </tr>
        <tr>
            <th colspan="{{ $totalCols }}"></th>
        </tr>

        {{-- HEADER TABLE --}}
        <tr>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">No</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">
                Tanggal</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Shift
            </th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">No.
                Batch</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">
                Prioritas</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Nama
                Produk</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Warna
            </th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Mesin
            </th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">
                Operator</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">
                Koordinator</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Target
            </th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Jam
                Kerja</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Total
                Mnt</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">CT
                (Std)</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">CT
                (Act)</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Cav
                (Std)</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Cav
                (Act)</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Qty
                Theory</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Qty
                Good</th>
            <th
                style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center; color: red;">
                Qty Reject</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Total
                Output</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">
                Purging</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">WIP
            </th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">
                Counter</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">
                Efisiensi</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">Yield
            </th>

            {{-- HEADER DINAMIS REJECT --}}
            @foreach ($masterRejects as $mReject)
                <th
                    style="background-color: #f2dede; border: 1px solid #000; font-weight: bold; text-align: center; width: 15px;">
                    {{ $mReject->name }}
                </th>
            @endforeach

            {{-- HEADER DINAMIS DOWNTIME --}}
            @foreach ($masterDowntimes as $mDowntime)
                <th
                    style="background-color: #fcf8e3; border: 1px solid #000; font-weight: bold; text-align: center; width: 15px;">
                    {{ $mDowntime->name }}
                </th>
            @endforeach

            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">
                Kemasan</th>
            <th style="background-color: #dce6f1; border: 1px solid #000; font-weight: bold; text-align: center;">
                Catatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reports as $r)
            @php
                $ctStd = $r->master_cycle_time ?? $r->cycle_time;
                $cavStd = $r->master_cavity ?? $r->cavity;
            @endphp
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid #000; text-align: center;">
                    {{ \Carbon\Carbon::parse($r->production_date)->format('d/m/Y') }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $r->shift->name ?? '-' }}</td>
                <td style="border: 1px solid #000;">{{ $r->batch->batch_code ?? '-' }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ ucfirst($r->batch->priority ?? '-') }}</td>
                <td style="border: 1px solid #000;">{{ $r->batch->product->name ?? '-' }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $r->batch->color->name ?? '-' }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $r->machine->name ?? '-' }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $r->operator->name ?? '-' }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $r->coordinator->name ?? '-' }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $r->batch->target_quantity }}</td>
                <td style="border: 1px solid #000; text-align: center;">
                    {{ \Carbon\Carbon::parse($r->start_time)->format('H:i') }} -
                    {{ \Carbon\Carbon::parse($r->end_time)->format('H:i') }}
                </td>
                <td style="border: 1px solid #000; text-align: center;">{{ $r->total_minutes }}</td>

                {{-- DATA STANDARD & ACTUAL --}}
                <td style="border: 1px solid #000; text-align: center;">{{ $ctStd }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $r->actual_cycle_time }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $cavStd }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $r->actual_cavity }}</td>

                <td style="border: 1px solid #000; text-align: right;">{{ $r->qty_theory }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $r->qty_good }}</td>
                <td style="border: 1px solid #000; text-align: right; color: red;">{{ $r->qty_reject_total }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $r->total_output }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $r->qty_purging }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $r->wip }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $r->total_counter }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ round($r->efficiency, 2) }}%</td>
                <td style="border: 1px solid #000; text-align: center;">{{ round($r->yield, 2) }}%</td>

                {{-- ISI DINAMIS REJECT (PIVOT) --}}
                @foreach ($masterRejects as $mReject)
                    @php
                        $rejectData = $r->rejects->firstWhere('reject_item_id', $mReject->id);
                        $qty = $rejectData ? $rejectData->qty : 0;
                        $bg = $qty > 0 ? '#fce4e4' : '#ffffff';
                    @endphp
                    <td style="border: 1px solid #000; text-align: center; background-color: {{ $bg }};">
                        {{ $qty > 0 ? $qty : '-' }}
                    </td>
                @endforeach

                {{-- ISI DINAMIS DOWNTIME (PIVOT) --}}
                @foreach ($masterDowntimes as $mDowntime)
                    @php
                        $downtimeData = $r->downtimes->firstWhere('downtime_id', $mDowntime->id);
                        $dur = $downtimeData ? $downtimeData->duration : 0;
                        $bg = $dur > 0 ? '#fff9c4' : '#ffffff';
                    @endphp
                    <td style="border: 1px solid #000; text-align: center; background-color: {{ $bg }};">
                        {{ $dur > 0 ? $dur : '-' }}
                    </td>
                @endforeach

                <td style="border: 1px solid #000; text-align: center;">{{ $r->packagingType->name ?? '-' }}
                    ({{ $r->packaging_qty }})</td>
                <td style="border: 1px solid #000;">{{ $r->notes }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
