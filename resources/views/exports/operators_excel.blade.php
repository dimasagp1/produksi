<table>
    <thead>
        <tr>
            <th colspan="3" style="text-align: center; font-weight: bold; font-size: 14px; height: 30px;">
                EK Dev
            </th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: center; font-size: 10px;">
                Jl. Industri Raya No. 123, Kawasan Industri Candi, Semarang
            </th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: center; font-size: 10px;">
                Telp: (024) 12345678 | Email: info@ekdevstudio.com
            </th>
        </tr>
        <tr>
            <th colspan="3"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #cccccc;">No
            </th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #cccccc;">Nama
                Operator</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #cccccc;">
                Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($operators as $key => $op)
            <tr>
                <td style="text-align: center; border: 1px solid #000000;">{{ $key + 1 }}</td>
                <td style="border: 1px solid #000000;">{{ $op->name }}</td>
                <td style="text-align: center; border: 1px solid #000000;">
                    {{ $op->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                </td>
            </tr>
        @endforeach

        <tr></tr>
        <tr>
            <td colspan="3" style="text-align: right; font-style: italic;">
                Dicetak oleh: {{ Auth::user()->name ?? 'Admin' }}
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: right; font-style: italic;">
                Tanggal Cetak: {{ now()->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
            </td>
        </tr>
    </tbody>
</table>
