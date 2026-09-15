@extends('layouts.app')

@section('title', 'MTBF & MTTR Analysis')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    {{-- CHART JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .saas-input {
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            width: 100%;
            background-color: #f8fafc;
        }

        .dark .saas-input {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        .saas-input:focus {
            outline: none;
            border-color: #3b82f6;
        }
    </style>

    {{-- HEADER INFO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 text-white shadow-lg">
            <h3 class="font-bold text-lg mb-1">MTBF (Mean Time Between Failures)</h3>
            <p class="text-blue-100 text-xs mb-4">Rata-rata waktu mesin beroperasi normal sebelum rusak.</p>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-arrow-trend-up text-3xl"></i>
                <span class="text-sm font-bold opacity-80">Target: Semakin TINGGI semakin baik</span>
            </div>
        </div>
        <div class="bg-gradient-to-r from-rose-600 to-rose-700 rounded-2xl p-6 text-white shadow-lg">
            <h3 class="font-bold text-lg mb-1">MTTR (Mean Time To Repair)</h3>
            <p class="text-rose-100 text-xs mb-4">Rata-rata waktu yang dibutuhkan untuk perbaikan.</p>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-arrow-trend-down text-3xl"></i>
                <span class="text-sm font-bold opacity-80">Target: Semakin RENDAH semakin baik</span>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <form action="{{ route('reliability.index') }}" method="GET"
            class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div
                    class="h-10 w-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center text-slate-500">
                    <i class="fa-solid fa-calendar"></i>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 dark:text-white">Periode Analisis</h2>
                    <p class="text-xs text-slate-500">Durasi: {{ $days }} Hari</p>
                </div>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <input type="text" name="start_date" class="saas-input h-10 date-picker cursor-pointer w-32"
                    value="{{ $startDate }}" placeholder="Start">
                <span class="self-center text-slate-400">-</span>
                <input type="text" name="end_date" class="saas-input h-10 date-picker cursor-pointer w-32"
                    value="{{ $endDate }}" placeholder="End">
                <button type="submit"
                    class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        </form>
    </div>

    {{-- CHART & TABLE --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COL 1: CHART --}}
        <div
            class="lg:col-span-1 bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700">
            <h4 class="font-bold text-slate-800 dark:text-white mb-4">Top 5 Mesin Paling Rentan</h4>
            <div class="relative h-64 w-full">
                <canvas id="mtbfChart"></canvas>
            </div>
            <p class="text-xs text-center text-slate-400 mt-2">*Berdasarkan MTBF terendah (Paling sering rusak)</p>
        </div>

        {{-- COL 2: TABLE --}}
        <div
            class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                <h4 class="font-bold text-slate-800 dark:text-white">Detail Performa Mesin</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                    <thead class="bg-slate-50/80 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Mesin</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Freq (Kali)</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Total Downtime</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-blue-600 uppercase">MTBF (Jam)</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-rose-600 uppercase">MTTR (Menit)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach ($analysisData as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">{{ $item['name'] }}</td>
                                <td class="px-6 py-4 text-center text-sm text-slate-600">{{ $item['freq'] }}</td>
                                <td class="px-6 py-4 text-center text-sm text-slate-600">
                                    {{ number_format($item['downtime']) }} m</td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ number_format($item['mtbf'], 1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 rounded text-xs font-bold {{ $item['mttr'] > 60 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ number_format($item['mttr'], 1) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            if (typeof flatpickr !== 'undefined') flatpickr(".date-picker", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y"
            });

            // CONFIG CHART
            const ctx = document.getElementById('mtbfChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData->pluck('name')),
                    datasets: [{
                        label: 'MTBF (Jam)',
                        data: @json($chartData->pluck('mtbf')),
                        backgroundColor: '#3b82f6', // Brand Blue
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y', // Horizontal Bar
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jam Operasi'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
