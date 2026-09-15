<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data Jenis Kemasan</title>
    <style>
        /* Margin halaman: Atas 100px (untuk kop), Bawah 60px (untuk footer) */
        @page {
            margin: 100px 25px 60px 25px;
        }

        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        /* HEADER (KOP SURAT) */
        header {
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            height: 80px;
        }

        /* FOOTER */
        footer {
            position: fixed;
            bottom: -40px;
            left: 0px;
            right: 0px;
            height: 30px;
            text-align: right;
            font-size: 9px;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        /* Styling Kop Surat */
        .kop-table {
            width: 100%;
            border: none;
            margin-bottom: 5px;
        }

        .kop-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .company-info {
            text-align: center;
            padding: 0 20px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .company-address {
            font-size: 10px;
            color: #555;
        }

        .line-separator {
            border-bottom: 3px double #333;
            margin-top: 5px;
        }

        /* TABEL DATA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
        }

        .data-table th {
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-size: 10px;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .page-number:after {
            content: counter(page);
        }
    </style>
</head>

<body>

    <header>
        <table class="kop-table">
            <tr>
                <td width="80" align="center">
                    <img src="{{ app_logo_path() }}" width="60" style="object-fit: contain;">
                </td>
                <td class="company-info">
                    <div class="company-name">EK Dev</div>
                    <div class="company-address">
                        Jl. Industri Raya No. 123, Kawasan Industri Candi, Semarang<br>
                        Telp: (024) 12345678 | Email: info@ekdevstudio.com
                    </div>
                </td>
                <td width="80"></td>
            </tr>
        </table>
        <div class="line-separator"></div>
    </header>

    <footer>
        Dicetak oleh: <b>{{ Auth::user()->name ?? 'Admin' }}</b> |
        Tanggal: {{ now()->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB |
        Halaman <span class="page-number"></span>
    </footer>

    <main>
        <h3 style="text-align: center; margin-top: 0; margin-bottom: 10px;">LAPORAN DATA JENIS KEMASAN</h3>

        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" style="text-align: center">No</th>
                    <th>Nama Kemasan</th>
                    <th width="25%" style="text-align: center">Isi Per Unit</th>
                    <th width="15%" style="text-align: center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packagingTypes as $key => $type)
                    <tr>
                        <td style="text-align: center">{{ $key + 1 }}</td>
                        <td>{{ $type->name }}</td>
                        <td style="text-align: center">
                            {{ number_format($type->conversion_quantity, 0, ',', '.') }} {{ $type->content_unit }}
                        </td>
                        <td style="text-align: center">
                            {{ $type->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>

</body>

</html>
