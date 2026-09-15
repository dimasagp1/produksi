@extends('layouts.app')

@section('title', 'QC Analysis - Trend & Pareto')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    {{-- LOAD CHART.JS --}}
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

    {{-- TOOLBAR FILTER --}}
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700 mb-6">
        <form action="{{ route('qc-analysis.index') }}" method="GET"
            class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="h-10 w-10 bg-rose-50 dark:bg-rose-900/20 text-rose-600 flex items-center justify-center rounded-xl">
                    <i class="fa-solid fa-chart-line text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 dark:text-white">Analisis Cacat</h2>
                    <p class="text-xs text-slate-500">Total Reject Periode Ini: <span
                            class="font-bold text-rose-600">{{ number_format($grandTotalReject) }} Pcs</span></p>
                </div>
            </div>

            <div class="flex gap-2 w-full md:w-auto items-center">
                <input type="text" name="start_date" class="saas-input h-10 date-picker cursor-pointer w-32"
                    value="{{ $startDate }}" placeholder="Start">
                <span class="text-slate-400">-</span>
                <input type="text" name="end_date" class="saas-input h-10 date-picker cursor-pointer w-32"
                    value="{{ $endDate }}" placeholder="End">
                <button type="submit"
                    class="h-10 px-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md transition-all">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- 1. PARETO CHART --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white"><i
                        class="fa-solid fa-chart-bar mr-2 text-indigo-500"></i>Pareto Chart (Jenis Cacat)</h3>
                <span class="text-[10px] text-slate-400 uppercase tracking-wider">Hukum 80/20</span>
            </div>
            <div class="relative h-80 w-full">
                <canvas id="paretoChart"></canvas>
            </div>
        </div>

        {{-- 2. TREND CHART --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-subtle border border-slate-200 dark:border-slate-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white"><i
                        class="fa-solid fa-arrow-trend-up mr-2 text-emerald-500"></i>Trend Reject Rate (%)</h3>
                <span class="text-[10px] text-slate-400 uppercase tracking-wider">Harian</span>
            </div>
            <div class="relative h-80 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

    </div>

    <script>
        window.addEventListener('load', function() {
            // Init Datepicker
            if (typeof flatpickr !== 'undefined') flatpickr(".date-picker", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y"
            });

            // --- 1. PARETO CHART CONFIG ---
            const ctxPareto = document.getElementById('paretoChart').getContext('2d');
            new Chart(ctxPareto, {
                type: 'bar',
                data: {
                    labels: @json($paretoChartData['labels']),
                    datasets: [{
                            label: 'Kumulatif %',
                            data: @json($paretoChartData['data_line']),
                            type: 'line',
                            borderColor: '#ef4444', // Red 500
                            borderWidth: 2,
                            yAxisID: 'y1',
                            pointStyle: 'circle',
                            pointRadius: 3,
                            tension: 0.1
                        },
                        {
                            label: 'Jumlah Reject',
                            data: @json($paretoChartData['data_bar']),
                            backgroundColor: '#6366f1', // Indigo 500
                            borderRadius: 4,
                            yAxisID: 'y'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Qty (Pcs)'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            max: 100,
                            title: {
                                display: true,
                                text: 'Persentase (%)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });

            // --- 2. TREND CHART CONFIG ---
            const ctxTrend = document.getElementById('trendChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: @json($trendChartData['labels']),
                    datasets: [{
                            label: 'Reject Rate (%)',
                            data: @json($trendChartData['rate']),
                            borderColor: '#10b981', // Emerald 500
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Qty Reject',
                            data: @json($trendChartData['qty']),
                            type: 'bar',
                            backgroundColor: 'rgba(244, 63, 94, 0.3)', // Rose
                            yAxisID: 'y1',
                            barThickness: 10
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Rate (%)'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Qty (Pcs)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
