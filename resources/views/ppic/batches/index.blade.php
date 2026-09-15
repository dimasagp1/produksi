@extends('layouts.app')

@section('title', 'SPK / Batch')

@section('content')
    {{-- 1. Styles & Libraries --}}
    @include('ppic.batches.partials.styles')

    {{-- 2. PHP Logic (Global Variables) --}}
    @php
        $statTotal = \App\Models\Batch::count();
        $statRunning = \App\Models\Batch::where('status', 'running')->count();
        $statPlanning = \App\Models\Batch::where('status', 'planning')->count();
        $statCompleted = \App\Models\Batch::where('status', 'completed')->count();
        $statActive = \App\Models\Batch::where('is_active', 1)->count();
        $statNonActive = \App\Models\Batch::where('is_active', 0)->count();
        $currentStatus = request('status');
        $currentGroup = request('status_group');
    @endphp

    <div class="w-full font-sans text-slate-600 dark:text-slate-300">

        {{-- 3. Header & Summary Stats --}}
        @include('ppic.batches.partials.stats')

        {{-- 4. Toolbar (Filter, Search, Export) --}}
        @include('ppic.batches.partials.toolbar')

        {{-- 5. Main List Data --}}
        @include('ppic.batches.partials.list')

    </div>

    {{-- 6. Modals (Create, Edit, View, Import) --}}
    @include('ppic.batches.partials.modals')

    {{-- 7. Scripts --}}
    @include('ppic.batches.partials.scripts')

@endsection
