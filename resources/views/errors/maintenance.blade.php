<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode | {{ app_setting('app_name', 'System Update') }}</title>
    <link rel="shortcut icon" href="{{ app_favicon_url() }}" type="image/x-icon">
    <link rel="icon" href="{{ app_favicon_url() }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            /* Slate-900 */
            color: #e2e8f0;
            /* Slate-200 */
        }

        /* Background Grid Pattern */
        .bg-grid-pattern {
            background-image: linear-gradient(to right, #1e293b 1px, transparent 1px),
                linear-gradient(to bottom, #1e293b 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(circle at center, black 40%, transparent 100%);
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 100%);
        }

        /* Glassmorphism Card */
        .glass-card {
            background: rgba(30, 41, 59, 0.4);
            /* Slate-800 low opacity */
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Floating Animation */
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        /* Gradient Text */
        .text-gradient {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            /* Amber Gradient */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="h-screen w-full flex items-center justify-center overflow-hidden relative">

    {{-- Latar Belakang Grid --}}
    <div class="absolute inset-0 bg-grid-pattern z-0 opacity-40"></div>

    {{-- Efek Glow di Tengah --}}
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-amber-500/10 rounded-full blur-[120px] -z-10">
    </div>

    {{-- Main Container --}}
    <div
        class="relative z-10 glass-card p-8 md:p-12 rounded-3xl max-w-lg w-full mx-4 text-center border-t border-white/10">

        {{-- Icon & Status Badge --}}
        <div class="flex justify-center mb-8 relative">
            <div class="relative animate-float">
                <div class="absolute inset-0 bg-amber-500/20 blur-xl rounded-full"></div>
                <div class="relative bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-xl">
                    <i class="fas fa-screwdriver-wrench text-5xl text-amber-500"></i>
                </div>
                {{-- Status Badge Kecil --}}
                <div
                    class="absolute -bottom-2 -right-2 bg-slate-800 border border-slate-600 px-2 py-0.5 rounded-full flex items-center gap-1.5 shadow-lg">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Maintenance</span>
                </div>
            </div>
        </div>

        {{-- Judul & Deskripsi --}}
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3 tracking-tight">
            Sistem Sedang <span class="text-gradient">Diperbarui</span>
        </h1>

        <p class="text-slate-400 text-sm md:text-base leading-relaxed mb-8 px-4">
            Kami sedang melakukan peningkatan performa dan pemeliharaan sistem.
            Mohon maaf atas ketidaknyamanan ini, silakan kembali beberapa saat lagi.
        </p>

        {{-- Action Buttons --}}
        <div class="flex flex-col gap-3">
            @auth
                {{-- Jika User Login --}}
                <div
                    class="bg-slate-800/50 rounded-xl p-3 border border-slate-700/50 mb-2 flex items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-8 w-8 rounded-full bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center font-bold text-xs text-white">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-slate-400">Login sebagai</p>
                            <p class="text-sm font-bold text-white truncate max-w-[150px]">{{ auth()->user()->name }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-xs font-medium text-red-400 hover:text-red-300 transition-colors">
                            Logout <i class="fas fa-sign-out-alt ml-1"></i>
                        </button>
                    </form>
                </div>

                <button onclick="window.location.reload()"
                    class="w-full inline-flex justify-center items-center py-3 px-6 border border-transparent text-sm font-semibold rounded-xl text-white bg-amber-600 hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 focus:ring-offset-slate-900 transition-all shadow-lg shadow-amber-900/20">
                    <i class="fas fa-sync-alt mr-2 animate-spin-slow"></i> Coba Refresh Halaman
                </button>
            @else
                {{-- Jika Guest --}}
                <a href="{{ route('login') }}"
                    class="w-full inline-flex justify-center items-center py-3 px-6 border border-slate-700 text-sm font-semibold rounded-xl text-slate-300 bg-slate-800/50 hover:bg-slate-800 hover:text-white hover:border-slate-600 transition-all">
                    <i class="fas fa-lock mr-2"></i> Login Administrator
                </a>
            @endauth
        </div>

        {{-- Footer Info --}}
        <div
            class="mt-8 pt-6 border-t border-white/5 flex justify-between items-center text-[10px] font-mono text-slate-500 uppercase tracking-widest">
            <span>&copy; {{ date('Y') }} Etgar Kurniawan. All rights reserved.</span>
            <span>Est: ~15 Mins</span>
        </div>
    </div>

</body>

</html>
