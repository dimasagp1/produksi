@extends('layouts.app')

@section('title', 'Machine History')

@section('content')
    <style>
        .saas-input {
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0 16px;
            width: 100%;
            background-color: #f8fafc;
        }

        .saas-input:focus {
            outline: none;
            border-color: #3b82f6;
        }
    </style>

    {{-- TOOLBAR --}}
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-subtle mb-6">
        <form action="{{ route('machine-history.index') }}" method="GET" class="flex justify-between items-center">
            <h2 class="font-bold text-lg text-slate-800 dark:text-white">Daftar Mesin</h2>
            <div class="relative w-64">
                <input type="text" name="search" value="{{ request('search') }}" class="saas-input h-10 !pl-9"
                    placeholder="Cari Mesin...">
                <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400"></i>
            </div>
        </form>
    </div>

    {{-- GRID MESIN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($machines as $machine)
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-subtle border border-slate-200 dark:border-slate-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 group-hover:bg-brand-50 group-hover:text-brand-600 transition-colors">
                        <i class="fa-solid fa-industry text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white truncate w-40">{{ $machine->name }}</h3>
                        <p class="text-xs text-slate-500">{{ $machine->code ?? '-' }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700 pt-4 mt-2">
                    <a href="{{ route('machine-history.show', $machine->id) }}"
                        class="block w-full text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold rounded-xl transition-colors text-sm">
                        Lihat Riwayat <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $machines->links() }}
    </div>
@endsection
