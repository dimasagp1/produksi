<table>
    <thead>
        <tr>
            {{-- colspan="4" karena ada 4 kolom: No, Nama Produk, Info Mesin, Status --}}
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 14px; height: 30px;">
                EK Dev
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-size: 10px;">
                Jl. Industri Raya No. 123, Kawasan Industri Candi, Semarang
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-size: 10px;">
                Telp: (024) 12345678 | Email: info@ekdevstudio.com
            </th>
        </tr>
        <tr>
            <th colspan="4"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #cccccc;">No
            </th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #cccccc;">Nama
                Produk</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #cccccc;">Info
                Mesin (Cycle Time | Cavity)</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #cccccc;">
                Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($products as $key => $product)
            <tr>
                <td style="text-align: center; border: 1px solid #000000; vertical-align: top;">{{ $key + 1 }}</td>
                <td style="border: 1px solid #000000; vertical-align: top;">{{ $product->name }}</td>
                <td style="border: 1px solid #000000; vertical-align: top;">
                    @if ($product->machines->isEmpty())
                        -
                    @else
                        @foreach ($product->machines as $machine)
                            {{ $machine->name }} (CT: {{ $machine->pivot->cycle_time }}s | Cav:
                            {{ $machine->pivot->cavity }})<br>
                        @endforeach
                    @endif
                </td>
                <td style="text-align: center; border: 1px solid #000000; vertical-align: top;">
                    {{-- Perhatikan: Status produk menggunakan 'aktif'/'nonaktif' (lowercase bahasa indonesia) --}}
                    {{ ucfirst($product->status) }}
                </td>
            </tr>
        @endforeach

        <tr></tr>
        <tr>
            <td colspan="4" style="text-align: right; font-style: italic;">
                Dicetak oleh: {{ Auth::user()->name ?? 'Admin' }}
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; font-style: italic;">
                Tanggal Cetak: {{ now()->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
            </td>
        </tr>
    </tbody>
</table>
