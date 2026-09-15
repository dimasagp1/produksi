<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - 404</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            /* Slate-50 */
        }

        /* Latar belakang Grid Halus ala SaaS */
        .bg-grid-pattern {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Animasi Gradient Teks */
        .text-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animasi Halus */
        .fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4 relative overflow-hidden bg-grid-pattern">

    {{-- Efek Glow di Background --}}
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-100 rounded-full blur-[100px] -z-10 opacity-60">
    </div>

    <div
        class="max-w-md w-full bg-white/80 backdrop-blur-xl border border-white/50 shadow-2xl rounded-2xl p-8 text-center fade-in-up">

        {{-- Ikon & Angka 404 --}}
        <div class="mb-6 relative inline-block">
            <div class="text-8xl font-black text-gradient select-none tracking-tighter">404</div>
            <div
                class="absolute -top-4 -right-4 bg-red-50 text-red-600 border border-red-100 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider rotate-12 shadow-sm">
                Error
            </div>
        </div>

        {{-- Pesan Utama --}}
        <h1 class="text-2xl font-bold text-slate-900 mb-3">Halaman Tidak Ditemukan</h1>
        <p class="text-slate-500 mb-8 text-sm leading-relaxed">
            Oops! Sepertinya Anda tersesat di antah berantah. Halaman yang Anda cari tidak tersedia atau telah
            dipindahkan.
        </p>

        {{-- Tombol Aksi --}}
        <div class="flex flex-col gap-3">
            {{-- Tombol Utama --}}
            <a href="{{ url('/') }}"
                class="w-full inline-flex items-center justify-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5 focus:ring-4 focus:ring-blue-200">
                <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
            </a>

            {{-- Tombol Sekunder --}}
            <button onclick="history.back()"
                class="w-full inline-flex items-center justify-center px-5 py-3 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all focus:ring-4 focus:ring-slate-100">
                <i class="fas fa-arrow-left mr-2"></i> Halaman Sebelumnya
            </button>
        </div>

        {{-- Footer Kecil --}}
        <div class="mt-8 pt-6 border-t border-slate-100">
            <p class="text-xs text-slate-400 font-medium">
                &copy; {{ date('Y') }} Etgar Kurniawan. All rights reserved.
            </p>
        </div>
    </div>

</body>

</html>
