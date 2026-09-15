<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIVE MONITOR - {{ strtoupper(app_setting('app_name', 'AEJ MANUFACTRA')) }}</title>
    <link rel="shortcut icon" href="{{ app_favicon_url() }}" type="image/x-icon">
    <link rel="icon" href="{{ app_favicon_url() }}" type="image/png">

    {{-- Libraries --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Inter:wght@400;600;800&display=swap"
        rel="stylesheet">

    {{-- Tailwind Config --}}
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['"Chakra Petch"', 'monospace'],
                    },
                    colors: {
                        slate: {
                            850: '#151f32',
                            900: '#0f172a',
                            950: '#020617'
                        }
                    }
                }
            }
        }
    </script>

    {{-- Custom CSS --}}
    <style>
        body {
            background-color: #020617;
            color: white;
            overflow: hidden;
        }

        .glass-panel {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .text-glow {
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        .apexcharts-tooltip {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #fff;
        }

        .apexcharts-tooltip-title {
            background: #0f172a !important;
            border-bottom: 1px solid #334155 !important;
        }
    </style>
</head>

<body class="h-screen flex flex-col p-3 gap-3">

    {{-- 1. HEADER --}}
    <div class="flex justify-between items-center px-2 shrink-0">
        <div class="flex items-center gap-4">
            <img src="{{ app_logo_url() }}" class="h-14 w-auto object-contain rounded-lg">
            <div>
                <h1 class="text-2xl font-black tracking-widest text-white uppercase leading-none">{{ app_setting('app_name', 'Live Production') }}</h1>
                <p class="text-slate-400 text-sm font-mono tracking-wide mt-0.5">{{ app_setting('company_name', 'PT Abhimata Emas Juara') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-6 bg-slate-900/50 px-6 py-2 rounded-full border border-slate-800">
            <div id="date-display" class="text-xl font-bold text-slate-300 uppercase tracking-wide">
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
            <div class="h-8 w-px bg-slate-700"></div>
            <div id="clock" class="text-4xl font-mono font-bold text-emerald-400 text-glow"
                style="min-width: 180px; text-align: center;">
                00:00:00
            </div>
        </div>
    </div>

    {{-- 2. KPI CARDS --}}
    <div class="grid grid-cols-6 gap-3 shrink-0 h-32">
        @php
            function renderKpi($label, $id, $val, $icon, $color)
            {
                return "
                <div class='glass-panel rounded-xl p-3 flex flex-col justify-between relative overflow-hidden'>
                    <div class='absolute top-2 right-2 opacity-20'><i class='fa-solid $icon text-4xl $color'></i></div>
                    <span class='text-slate-400 uppercase tracking-widest font-bold text-[10px]'>$label</span>
                    <div id='$id' class='text-4xl font-mono font-bold text-white mt-1'>$val</div>
                </div>";
            }
        @endphp

        {!! renderKpi(
            'Target Plan',
            'tv-target',
            number_format($totalTarget, 0, ',', '.'),
            'fa-bullseye',
            'text-blue-500',
        ) !!}

        {{-- Achievement Card (No Outline) --}}
        <div class="glass-panel rounded-xl p-3 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-2 right-2 opacity-20"><i class="fa-solid fa-trophy text-4xl text-amber-500"></i>
            </div>
            <span class="text-slate-400 uppercase tracking-widest font-bold text-[10px]">Achievement</span>
            <div id="tv-achievement"
                class="text-4xl font-mono font-bold {{ $achievement >= 100 ? 'text-emerald-400' : 'text-amber-400' }}">
                {{ number_format($achievement, 1) }}<span class="text-xl">%</span>
            </div>
        </div>

        {!! renderKpi(
            'Actual Output',
            'tv-output',
            number_format($totalOutput, 0, ',', '.'),
            'fa-boxes-stacked',
            'text-indigo-500',
        ) !!}

        <div class='glass-panel rounded-xl p-3 flex flex-col justify-between relative overflow-hidden'>
            <div class='absolute top-2 right-2 opacity-20'><i
                    class='fa-solid fa-triangle-exclamation text-4xl text-red-500'></i></div>
            <span class='text-slate-400 uppercase tracking-widest font-bold text-[10px]'>Reject</span>
            <div id="tv-reject" class='text-4xl font-mono font-bold text-red-500 mt-1'>
                {{ number_format($totalReject, 0, ',', '.') }}</div>
        </div>

        {!! renderKpi('Avg Yield', 'tv-yield', number_format($avgYield, 1) . '%', 'fa-percent', 'text-purple-500') !!}
        {!! renderKpi(
            'Efisiensi',
            'tv-efficiency',
            number_format($avgEfficiency, 1) . '%',
            'fa-gauge-high',
            'text-teal-500',
        ) !!}
    </div>

    {{-- 3. CONTENT GRID --}}
    <div class="grid grid-cols-4 gap-3 flex-1 min-h-0">

        {{-- LEFT: TREND CHART --}}
        <div class="col-span-3 glass-panel rounded-xl p-4 flex flex-col relative">
            <h3 class="text-sm font-bold text-slate-300 mb-1 flex items-center gap-2 uppercase tracking-widest">
                <i class="fa-solid fa-chart-line text-blue-500"></i> Trend Output Produksi
            </h3>
            <div class="flex-1 w-full min-h-0 relative">
                <div id="chartTrendOutput" class="absolute inset-0"></div>
            </div>
        </div>

        {{-- RIGHT: TOP REJECTS & MACHINES (Lebar 1 Kolom) --}}
        <div class="col-span-1 flex flex-col gap-3 min-h-0">

            {{-- Top Rejects Table (Top 5 - Versi Besar) --}}
            <div class="glass-panel rounded-xl p-4 flex-[1.4] flex flex-col min-h-0 overflow-hidden">
                <h3
                    class="text-sm font-bold text-slate-300 mb-3 border-b border-slate-700/50 pb-2 flex items-center gap-2 uppercase shrink-0">
                    <i class="fa-solid fa-bug text-red-500 text-base"></i> Top 5 Reject
                </h3>

                {{-- Gunakan justify-between agar 5 baris mengisi penuh ruang vertikal --}}
                <div class="flex-1 flex flex-col justify-between">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-slate-700/50">
                            @foreach ($topRejects->take(5) as $reject)
                                <tr>
                                    {{-- Padding tetap py-2 agar tidak terlalu rapat --}}
                                    <td class="py-2 align-middle">
                                        {{-- 1. Nama Reject: Turun dari text-sm ke text-xs --}}
                                        <div class="font-bold text-white truncate w-48 text-xs mb-0.5"
                                            title="{{ $reject->reject_name }}">
                                            {{ $reject->reject_name }}
                                        </div>

                                        {{-- 2. Nama Produk: Turun dari text-xs ke text-[10px] --}}
                                        <div class="text-[10px] text-slate-400 truncate w-48 mb-0.5"
                                            title="{{ $reject->product_name }}">
                                            {{ $reject->product_name }}
                                        </div>

                                        {{-- 3. Nama Mesin: Turun dari text-xs ke text-[10px] --}}
                                        <div class="text-[10px] text-indigo-400 font-bold truncate w-48">
                                            <i class="fa-solid fa-gear text-[8px] mr-1"></i>{{ $reject->machine_name }}
                                        </div>
                                    </td>

                                    <td class="py-2 text-right align-middle">
                                        {{-- 4. Angka Total: Turun dari text-2xl ke text-xl --}}
                                        <span
                                            class="text-red-500 font-mono font-bold text-xl tracking-tight text-glow-red">
                                            {{ number_format($reject->total_qty) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Top Machines Chart --}}
            <div class="glass-panel rounded-xl p-4 flex-[0.6] flex flex-col min-h-0 relative">
                <h3
                    class="text-xs font-bold text-slate-300 mb-1 border-b border-slate-700/50 pb-2 flex items-center gap-2 uppercase shrink-0">
                    <i class="fa-solid fa-industry text-indigo-500"></i> 5 Mesin Teratas
                </h3>
                <div class="flex-1 w-full min-h-0 relative">
                    <div id="chartTopMachines" class="absolute inset-0"></div>
                </div>
            </div>

        </div>
    </div>

    {{-- 4. FOOTER (MARQUEE PROFESIONAL) --}}
    <div class="mt-auto glass-panel rounded-lg py-1 px-4 shrink-0">
        <marquee class="text-base font-mono text-emerald-400 font-bold flex gap-10">
            PRIORITASKAN KESELAMATAN KERJA (SAFETY FIRST) &bull;
            BUDAYAKAN 5R (RINGKAS, RAPI, RESIK, RAWAT, RAJIN) DI AREA KERJA &bull;
            ZERO ACCIDENT & ZERO DEFECT IS OUR GOAL &bull;
            KUALITAS ADALAH KUNCI KEPUASAN PELANGGAN
        </marquee>
    </div>

    {{-- SCRIPTS --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // 1. JAM & TANGGAL
            setInterval(() => {
                const now = new Date();
                document.getElementById('clock').innerText = now.toLocaleTimeString('en-GB');
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                document.getElementById('date-display').innerText = now.toLocaleDateString('id-ID',
                    options);
            }, 1000);

            // Auto Refresh Total (1 Jam)
            setTimeout(() => window.location.reload(), 3600000);

            // ----------------------------------------
            // CHART 1: TREND OUTPUT
            // ----------------------------------------
            var optionsTrend = {
                chart: {
                    id: 'trendChart',
                    type: 'area',
                    height: '100%',
                    width: '100%',
                    toolbar: {
                        show: false
                    },
                    background: 'transparent',
                    animations: {
                        enabled: true,
                        easing: 'linear',
                        dynamicAnimation: {
                            speed: 1000
                        }
                    }
                },
                theme: {
                    mode: 'dark'
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 0.6,
                        opacityTo: 0.1
                    }
                },
                series: [{
                        name: 'Actual Output',
                        data: @json($trendOutput->pluck('total'))
                    },
                    {
                        name: 'Target Theory',
                        data: @json($trendOutput->pluck('total_target'))
                    }
                ],
                colors: ['#6366f1', '#10b981'],
                dataLabels: {
                    enabled: true,
                    offsetY: -5,
                    style: {
                        fontSize: '10px',
                        colors: ['#c7d2fe', '#a7f3d0']
                    },
                    background: {
                        enabled: true,
                        foreColor: '#000',
                        borderRadius: 4,
                        padding: 3,
                        opacity: 0.5,
                        borderWidth: 0
                    },
                    formatter: function(val) {
                        return val.toLocaleString('id-ID');
                    }
                },
                xaxis: {
                    categories: @json($trendOutput->pluck('date')),
                    labels: {
                        style: {
                            fontSize: '11px',
                            fontFamily: 'Inter',
                            colors: '#94a3b8'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '11px',
                            colors: '#94a3b8'
                        },
                        formatter: (val) => new Intl.NumberFormat('id-ID', {
                            notation: "compact",
                            compactDisplay: "short"
                        }).format(val)
                    }
                },
                grid: {
                    borderColor: '#334155',
                    strokeDashArray: 4,
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    }
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px'
                }
            };
            var chartTrend = new ApexCharts(document.querySelector("#chartTrendOutput"), optionsTrend);
            chartTrend.render();


            // ----------------------------------------
            // CHART 2: TOP MACHINES
            // ----------------------------------------
            var machineNames = @json($topMachineProducts->take(5)->pluck('machine_name'));
            var machineTotals = @json($topMachineProducts->take(5)->pluck('total'));

            var optionsMachine = {
                chart: {
                    id: 'machineChart',
                    type: 'bar',
                    height: '100%',
                    width: '100%',
                    toolbar: {
                        show: false
                    },
                    background: 'transparent'
                },
                theme: {
                    mode: 'dark'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '60%',
                        borderRadius: 4,
                        distributed: true
                    }
                },
                dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: {
                        colors: ['#fff'],
                        fontSize: '10px',
                        fontWeight: 'bold'
                    },
                    formatter: function(val, opt) {
                        return opt.w.globals.labels[opt.dataPointIndex] + ": " + val.toLocaleString('id-ID')
                    },
                    offsetX: 0,
                },
                series: [{
                    name: 'Total Output',
                    data: machineTotals
                }],
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                xaxis: {
                    categories: machineNames,
                    labels: {
                        show: false
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        show: false
                    }
                },
                grid: {
                    show: false,
                    padding: {
                        top: -10,
                        right: 0,
                        bottom: -10,
                        left: -10
                    }
                },
                tooltip: {
                    enabled: false
                },
                legend: {
                    show: false
                }
            };
            var chartMachine = new ApexCharts(document.querySelector("#chartTopMachines"), optionsMachine);
            chartMachine.render();


            // ----------------------------------------
            // AJAX REALTIME
            // ----------------------------------------
            setInterval(function() {
                let start = "{{ $startDate->format('Y-m-d') }}";
                let end = "{{ $endDate->format('Y-m-d') }}";

                $.ajax({
                    url: "{{ route('dashboard.json') }}",
                    type: "GET",
                    data: {
                        start_date: start,
                        end_date: end
                    },
                    success: function(response) {
                        if ($('#tv-target').length) $('#tv-target').text(response.kpi
                            .total_target);
                        if ($('#tv-output').length) $('#tv-output').text(response.kpi
                            .total_output);
                        if ($('#tv-reject').length) $('#tv-reject').text(response.kpi
                            .total_reject);
                        if ($('#tv-yield').length) $('#tv-yield').text(response.kpi.avg_yield);
                        if ($('#tv-efficiency').length) $('#tv-efficiency').text(response.kpi
                            .avg_efficiency);

                        if ($('#tv-achievement').length) {
                            $('#tv-achievement').html(response.kpi.achievement.replace('%',
                                '<span class="text-xl">%</span>'));
                            let achVal = response.kpi.achievement_val;
                            let colorClass = achVal >= 100 ? 'text-emerald-400' :
                                'text-amber-400';
                            $('#tv-achievement').removeClass().addClass(
                                'text-4xl font-mono font-bold ' + colorClass);
                        }

                        if (response.charts && response.charts.trend) {
                            chartTrend.updateOptions({
                                xaxis: {
                                    categories: response.charts.trend.labels
                                }
                            });
                            chartTrend.updateSeries([{
                                    name: 'Actual Output',
                                    data: response.charts.trend.output
                                },
                                {
                                    name: 'Target Theory',
                                    data: response.charts.trend.target
                                }
                            ]);
                        }

                        if (response.charts && response.charts.machine) {
                            let cats = response.charts.machine.categories.slice(0, 5);
                            let flatCats = cats.map(c => Array.isArray(c) ? c[0] : c);
                            let sers = response.charts.machine.series.slice(0, 5);
                            chartMachine.updateOptions({
                                xaxis: {
                                    categories: flatCats
                                }
                            });
                            chartMachine.updateSeries([{
                                name: 'Total Output',
                                data: sers
                            }]);
                        }
                    }
                });
            }, 10000);
        });
    </script>
</body>

</html>
