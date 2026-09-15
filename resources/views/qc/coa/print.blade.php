<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COA - {{ $coa->coa_code }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .info-table td {
            padding: 4px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 150px;
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .result-table th,
        .result-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .result-table th {
            background-color: #f0f0f0;
            text-align: center;
        }

        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .sig-box {
            text-align: center;
            width: 200px;
        }

        .sig-line {
            border-bottom: 1px solid #000;
            margin-top: 60px;
            margin-bottom: 5px;
        }

        @media print {
            body {
                padding: 0;
            }

            button {
                display: none;
            }
        }
    </style>
</head>

<body>
    <button onclick="window.print()"
        style="position: fixed; top: 10px; right: 10px; padding: 10px 20px; cursor: pointer;">Cetak PDF</button>

    <div class="header">
        <h1>Certificate of Analysis</h1>
        <p>PT. NAMA PERUSAHAAN ANDA</p>
        <p>Jl. Industri No. 123, Kawasan Industri, Indonesia</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Product Name</td>
            <td>: {{ $coa->batch->product->name }}</td>
            <td class="label">COA Number</td>
            <td>: {{ $coa->coa_code }}</td>
        </tr>
        <tr>
            <td class="label">Batch Number</td>
            <td>: {{ $coa->batch->batch_code }}</td>
            <td class="label">Date of Report</td>
            <td>: {{ \Carbon\Carbon::parse($coa->report_date)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Manufacture Date</td>
            <td>: {{ \Carbon\Carbon::parse($coa->manufacture_date)->format('d F Y') }}</td>
            <td class="label">Customer</td>
            <td>: {{ $coa->customer_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Expiry Date</td>
            <td>: {{ \Carbon\Carbon::parse($coa->expiry_date)->format('d F Y') }}</td>
        </tr>
    </table>

    <table class="result-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 30%">Test Parameter</th>
                <th style="width: 20%">Method</th>
                <th style="width: 25%">Specification</th>
                <th style="width: 20%">Result</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($coa->items as $index => $item)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td>{{ $item->parameter }}</td>
                    <td style="text-align: center">{{ $item->method ?? '-' }}</td>
                    <td style="text-align: center">{{ $item->specification }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $item->result }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="font-size: 14px; margin-bottom: 20px;">
        <strong>Remarks:</strong> {{ $coa->remarks ?? 'The product mentioned above meets the specifications.' }}
    </div>

    <div class="signature">
        <div class="sig-box">
            <div>Approved By:</div>
            <div class="sig-line"></div>
            <div><strong>{{ $coa->approver_name }}</strong></div>
            <div>QA Manager</div>
        </div>
    </div>
</body>

</html>
