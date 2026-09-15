<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ app_setting('app_name', 'AEJ ProductionApp') }}</title>

    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ app_favicon_url() }}" type="image/x-icon">
    <link rel="icon" href="{{ app_favicon_url() }}" type="image/png">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ url('/pwa-icon/192.png') }}?v={{ md5(app_setting('app_name', 'app') . app_logo_url()) }}">
    <link rel="manifest" href="{{ route('pwa.webmanifest') }}?v={{ md5(app_setting('app_name', 'app') . app_logo_url()) }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- Tailwind & Icons --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    },
                    animation: {
                        'beat': 'beat 1s infinite',
                    },
                    keyframes: {
                        beat: {
                            '0%, 100%': {
                                transform: 'scale(1)'
                            },
                            '50%': {
                                transform: 'scale(1.2)'
                            },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Pola Titik (Dot Pattern) yang lebih tegas */
        .bg-pattern {
            background-image: radial-gradient(#94a3b8 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>

<body
    class="h-full font-sans text-slate-600 antialiased selection:bg-primary-100 selection:text-primary-900 bg-slate-50 relative overflow-hidden">

    {{-- BACKGROUND DECORATION --}}
    <div class="fixed inset-0 z-0 pointer-events-none">
        {{-- Base Gradient yang lebih kontras --}}
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-white to-blue-100"></div>

        {{-- Dot Pattern --}}
        <div class="absolute inset-0 bg-pattern opacity-30"></div>

        {{-- Glowing Blobs (Warna dipertajam agar jelas) --}}
        <div class="absolute -top-[10%] -left-[5%] w-[40%] h-[50%] rounded-full bg-indigo-500/20 blur-[100px]"></div>
        <div class="absolute bottom-[-10%] -right-[5%] w-[50%] h-[60%] rounded-full bg-blue-500/20 blur-[100px]"></div>
    </div>

    {{-- WRAPPER UTAMA --}}
    <div class="min-h-screen w-full flex items-center justify-center p-4 sm:p-8 relative z-10">

        {{-- CARD CONTAINER (Putih Solid & Shadow Kuat) --}}
        <div
            class="flex w-full max-w-[1000px] bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(8,_112,_184,_0.1)] border border-slate-100 overflow-hidden min-h-[600px]">

            {{-- KOLOM KIRI: FORM LOGIN --}}
            <div class="w-full lg:w-1/2 flex flex-col justify-center p-8 sm:p-12 lg:p-16 relative z-10 bg-white">
                <div class="mx-auto w-full max-w-sm">

                    {{-- Header / Logo Section --}}
                    <div class="mb-10">
                        <div class="flex items-center gap-4 mb-8">
                            <img src="{{ app_logo_url() }}" alt="{{ app_setting('app_name', 'AEJ Logo') }}"
                                onerror="this.onerror=null; this.src='{{ asset('images/aej.png') }}';"
                                class="h-14 w-auto object-contain rounded-lg">
                            <span class="font-display font-bold text-2xl text-slate-900 tracking-tight">
                                {{ app_setting('app_name', 'AEJ ProductionApp') }}
                            </span>
                        </div>
                        <h2 class="text-3xl font-display font-bold tracking-tight text-slate-900">Selamat Datang</h2>
                        <p class="mt-2 text-sm text-slate-500">
                            Silakan masuk untuk mengakses sistem produksi.
                        </p>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- EMAIL FIELD --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold leading-6 text-slate-900">Email
                                Perusahaan</label>
                            <div class="mt-2 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-regular fa-envelope text-slate-400"></i>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required
                                    class="block w-full rounded-lg border-0 py-3 pl-10 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 transition-all bg-slate-50 hover:bg-white"
                                    placeholder="Masukkan Email Anda" value="{{ old('email') }}">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- PASSWORD FIELD --}}
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-sm font-semibold leading-6 text-slate-900">Kata
                                Sandi</label>
                            <div class="mt-2 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-lock text-slate-400"></i>
                                </div>
                                <input id="password" name="password" :type="show ? 'text' : 'password'"
                                    autocomplete="current-password" required
                                    class="block w-full rounded-lg border-0 py-3 pl-10 pr-10 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 transition-all bg-slate-50 hover:bg-white"
                                    placeholder="••••••••">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-slate-400 hover:text-slate-600"
                                    @click="show = !show">
                                    <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember-me" name="remember-me" type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600 bg-white">
                                <label for="remember-me" class="ml-2 block text-sm leading-6 text-slate-700">Ingat
                                    saya</label>
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                class="flex w-full justify-center items-center gap-2 rounded-lg bg-primary-600 px-3 py-3 text-sm font-bold leading-6 text-white shadow-md shadow-primary-500/30 hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all hover:-translate-y-0.5">
                                Masuk
                                <i class="fa-solid fa-right-to-bracket"></i>
                            </button>
                        </div>
                    </form>

                    {{-- Footer --}}
                    <div class="mt-10 border-t border-slate-200 pt-6">
                        <p class="text-xs text-center text-slate-500 font-medium">
                            &copy; {{ date('Y') }} {{ app_footer_text() }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: GAMBAR BANNER --}}
            <div class="relative hidden lg:block lg:w-1/2 bg-slate-900">
                <img class="absolute inset-0 h-full w-full object-cover opacity-90"
                    src="{{ asset('images/banner.jpg') }}" alt="AEJ Production Banner">

                {{-- Overlay Gradient Gelap agar Teks Putih Terbaca Jelas --}}
                <div
                    class="absolute inset-0 bg-slate-900/40 bg-gradient-to-t from-slate-900/95 via-slate-900/50 to-transparent">
                </div>

                {{-- Teks Overlay di Atas Gambar --}}
                <div class="absolute bottom-0 left-0 right-0 p-12 text-white z-20">
                    <div class="max-w-md">
                        <div
                            class="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur-md mb-6 shadow-sm">
                            <span class="mr-2 h-1.5 w-1.5 rounded-full bg-green-400 animate-pulse"></span>
                            System Operational
                        </div>
                        <h1
                            class="text-4xl font-display font-bold tracking-tight mb-4 leading-tight text-white drop-shadow-md">
                            Optimalkan Produksi,<br>Maksimalkan Hasil.
                        </h1>
                        <p class="text-base text-slate-200 leading-relaxed opacity-90 drop-shadow-sm">
                            Platform terintegrasi untuk memantau kinerja produksi, manajemen SPK, dan pelaporan harian
                            secara real-time.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- SweetAlert Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Login Berhasil',
                    text: '{{ session('success') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#ffffff',
                    iconColor: '#3b82f6',
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Masuk',
                    text: 'Email atau password yang Anda masukkan salah.',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'Coba Lagi',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'rounded-lg px-5 py-2.5 font-bold'
                    }
                });
            @endif
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js?v=4');
            });
        }
    </script>
</body>

</html>