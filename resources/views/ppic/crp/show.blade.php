@extends('layouts.app')

@section('title', 'Capacity Requirements Planning (CRP)')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="flex flex-col gap-8 fade-in-up">

        {{-- 1. HEADER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('mps.index') }}"
                    class="h-12 w-12 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm text-slate-500 hover:text-purple-600 hover:border-purple-200 transition-all active:scale-95">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
                            Analisis Kapasitas (CRP)
                        </h1>
                        <span
                            class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-600 border border-purple-100">
                            Capacity
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                        Periode: <span
                            class="font-mono text-purple-600 font-bold">{{ $plan->period->translatedFormat('F Y') }}</span>
                    </p>
                </div>
            </div>

            <form id="crpForm" action="{{ route('crp.generate', $plan->id) }}" method="POST">
                @csrf
                <button type="button" onclick="confirmCrp()"
                    class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-2xl shadow-lg shadow-purple-500/30 transition-all active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Hitung Kapasitas</span>
                </button>
            </form>
        </div>

        {{-- 2. CONTENT SECTION --}}
        @if ($capacities->isEmpty())
            {{-- EMPTY STATE --}}
            <div
                class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-800 rounded-[2.5rem] border border-dashed border-slate-300 dark:border-slate-700">
                <div class="h-24 w-24 bg-purple-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-chart-simple text-4xl text-purple-400"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Analisis Belum Tersedia</h3>
                <p class="text-slate-500 dark:text-slate-400 text-center max-w-md mb-8">
                    Lakukan perhitungan untuk melihat beban kerja vs kapasitas tersedia pada setiap Work Center.
                </p>
                <button type="button" onclick="confirmCrp()"
                    class="px-8 py-3 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 hover:text-purple-600 transition-all shadow-sm">
                    Mulai Analisis Sekarang
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI: CHART --}}
                <div
                    class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-200/40 dark:shadow-slate-900/40">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Beban Kerja vs Kapasitas</h3>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Satuan: Jam</span>
                    </div>
                    <div class="relative h-80 w-full">
                        <canvas id="crpChart"></canvas>
                    </div>
                </div>

                {{-- KOLOM KANAN: SUMMARY --}}
                <div class="space-y-6">
                    @php $overloads = $capacities->where('status', 'overload'); @endphp

                    @if ($overloads->count() > 0)
                        {{-- ALERT: OVERLOAD --}}
                        <div
                            class="bg-rose-50 dark:bg-rose-900/20 p-6 rounded-[2rem] border border-rose-200 dark:border-rose-800 relative overflow-hidden group">
                            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <i class="fa-solid fa-triangle-exclamation text-6xl text-rose-600"></i>
                            </div>

                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="h-10 w-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                                <h4 class="font-bold text-rose-700 dark:text-rose-400 text-lg">Overload!</h4>
                            </div>

                            <p class="text-sm text-rose-600 dark:text-rose-300 mb-4 font-medium">
                                Work center berikut melebihi kapasitas tersedia:
                            </p>

                            <ul class="space-y-2">
                                @foreach ($overloads as $ov)
                                    <li
                                        class="flex justify-between items-center bg-white/50 dark:bg-slate-900/30 p-2 rounded-lg border border-rose-100 dark:border-rose-900/50">
                                        <span
                                            class="text-xs font-bold text-rose-800 dark:text-rose-200">{{ $ov->work_center->name }}</span>
                                        <span
                                            class="text-xs font-mono font-bold text-rose-600">{{ number_format($ov->utilization_percentage) }}%</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        {{-- ALERT: SAFE --}}
                        <div
                            class="bg-emerald-50 dark:bg-emerald-900/20 p-6 rounded-[2rem] border border-emerald-200 dark:border-emerald-800 relative overflow-hidden group">
                            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <i class="fa-solid fa-thumbs-up text-6xl text-emerald-600"></i>
                            </div>

                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <h4 class="font-bold text-emerald-700 dark:text-emerald-400 text-lg">Kapasitas Aman</h4>
                            </div>

                            <p class="text-sm text-emerald-600 dark:text-emerald-300 font-medium leading-relaxed">
                                Seluruh Work Center memiliki kapasitas yang cukup untuk menangani target produksi periode
                                ini.
                            </p>
                        </div>
                    @endif

                    {{-- STATISTIK SINGKAT --}}
                    <div
                        class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-sm">
                        <h4 class="font-bold text-slate-800 dark:text-white mb-4">Statistik Utilisasi</h4>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-xs mb-1 font-bold text-slate-500">
                                    <span>Rata-rata Utilisasi</span>
                                    <span>{{ number_format($capacities->avg('utilization_percentage'), 1) }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-purple-500 h-2 rounded-full"
                                        style="width: {{ min($capacities->avg('utilization_percentage'), 100) }}%"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div class="text-center p-2 bg-slate-50 rounded-xl">
                                    <span class="block text-[10px] text-slate-400 uppercase font-bold">Tertinggi</span>
                                    <span
                                        class="block text-sm font-black text-slate-700">{{ number_format($capacities->max('utilization_percentage'), 1) }}%</span>
                                </div>
                                <div class="text-center p-2 bg-slate-50 rounded-xl">
                                    <span class="block text-[10px] text-slate-400 uppercase font-bold">Terendah</span>
                                    <span
                                        class="block text-sm font-black text-slate-700">{{ number_format($capacities->min('utilization_percentage'), 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABEL DETAIL --}}
                <div
                    class="lg:col-span-3 bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-200/40 dark:shadow-slate-900/40 overflow-hidden">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Detail Kapasitas per Work Center</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    {{-- 1. KOLOM NO --}}
                                    <th
                                        class="px-6 py-5 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-widest w-12">
                                        No</th>
                                    <th
                                        class="px-6 py-5 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">
                                        Work Center</th>
                                    <th
                                        class="px-6 py-5 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">
                                        Load (Jam)</th>
                                    <th
                                        class="px-6 py-5 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">
                                        Kapasitas (Jam)</th>
                                    <th
                                        class="px-6 py-5 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">
                                        Utilisasi</th>
                                    <th
                                        class="px-6 py-5 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-widest">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                                @foreach ($capacities as $cap)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">

                                        {{-- 1. ISI NO --}}
                                        <td class="px-6 py-4 text-center text-sm font-bold text-slate-500">
                                            {{ $loop->iteration }}
                                        </td>

                                        {{-- 2. WORK CENTER (ID HIDDEN) --}}
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800 dark:text-white text-sm">
                                                {{ $cap->work_center->name }}
                                            </div>
                                            {{-- ID DIHAPUS DARI SINI --}}
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <span class="font-mono font-bold text-slate-700 dark:text-slate-300 text-sm">
                                                {{ number_format($cap->required_hours, 1) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-mono font-bold text-slate-500 text-sm">
                                                {{ number_format($cap->available_hours, 1) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                                    <div class="h-full {{ $cap->utilization_percentage > 100 ? 'bg-rose-500' : ($cap->utilization_percentage < 50 ? 'bg-amber-400' : 'bg-emerald-500') }}"
                                                        style="width: {{ min($cap->utilization_percentage, 100) }}%"></div>
                                                </div>
                                                <span
                                                    class="text-xs font-bold w-12 text-right">{{ number_format($cap->utilization_percentage, 0) }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($cap->status == 'overload')
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 text-[10px] font-bold uppercase tracking-wide border border-rose-200 dark:border-rose-800">
                                                    <i class="fa-solid fa-triangle-exclamation"></i> Overload
                                                </span>
                                            @elseif($cap->status == 'underload')
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 text-[10px] font-bold uppercase tracking-wide border border-amber-100 dark:border-amber-800">
                                                    <i class="fa-solid fa-battery-quarter"></i> Idle
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wide border border-emerald-200 dark:border-emerald-800">
                                                    <i class="fa-solid fa-check"></i> Optimal
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- CHART JS SCRIPT --}}
            <script>
                const ctx = document.getElementById('crpChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartData['labels'] ?? []) !!},
                        datasets: [{
                                label: 'Load (Beban)',
                                data: {!! json_encode($chartData['load'] ?? []) !!},
                                backgroundColor: '#8b5cf6', // Violet-500
                                borderRadius: 6,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Kapasitas Max',
                                data: {!! json_encode($chartData['capacity'] ?? []) !!},
                                backgroundColor: '#cbd5e1', // Slate-300
                                type: 'line', // Garis batas kapasitas
                                borderColor: '#475569', // Slate-600
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointRadius: 0,
                                pointHoverRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 11
                                    },
                                    usePointStyle: true,
                                    boxWidth: 8
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                titleFont: {
                                    family: "'Inter', sans-serif",
                                    size: 13
                                },
                                bodyFont: {
                                    family: "'Inter', sans-serif",
                                    size: 12
                                },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9',
                                    borderDash: [2, 2]
                                },
                                ticks: {
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 10
                                    },
                                    color: '#64748b'
                                },
                                title: {
                                    display: true,
                                    text: 'Jam Kerja',
                                    color: '#94a3b8',
                                    font: {
                                        size: 10,
                                        weight: 'bold'
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 10
                                    },
                                    color: '#64748b'
                                }
                            }
                        }
                    }
                });
            </script>
        @endif
    </div>

    {{-- SCRIPT: SweetAlert Notification & Confirmation --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    title: 'Validasi Gagal',
                    html: `{!! session('error') !!}`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Lengkapi Routing',
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl font-bold px-6 py-2.5',
                        cancelButton: 'rounded-xl font-bold px-6 py-2.5'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('products.index') }}";
                    }
                });
            @endif
        });

        function confirmCrp() {
            Swal.fire({
                title: 'Jalankan Analisis CRP?',
                text: "Sistem akan menghitung beban kerja berdasarkan data MPS dan Routing saat ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#9333ea',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Analisis!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl font-bold px-6 py-2.5',
                    cancelButton: 'rounded-xl font-bold px-6 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('crpForm');

                    Swal.fire({
                        title: 'Sedang Menganalisis...',
                        text: 'Mohon tunggu, menghitung load per Work Center.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    form.submit();
                }
            });
        }
    </script>
@endsection
