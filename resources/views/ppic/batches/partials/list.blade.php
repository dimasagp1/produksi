<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
    @forelse ($batches as $batch)
        @php
            $isLocked = in_array($batch->status, ['completed', 'canceled']);
            $statusClass = match ($batch->status) {
                'running' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                'planning' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                'completed' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400',
                'hold' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400',
                'canceled' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',
            };
            $prioClass = match ($batch->priority) {
                'high'
                    => 'text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-900/30 bg-rose-50 dark:bg-rose-900/20',
                'medium'
                    => 'text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/30 bg-amber-50 dark:bg-amber-900/20',
                'low'
                    => 'text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900/30 bg-emerald-50 dark:bg-emerald-900/20',
                default
                    => 'text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800',
            };

            $totalSample = $batch->reports->sum('qty_sample');
            $lastReport = $batch->reports->sortByDesc('created_at')->first();
            $wip = $lastReport ? $lastReport->wip : 0;
            $counter = $lastReport ? $lastReport->total_counter : 0;
            $fg = $batch->reports->sum('qty_good');

            // PERBAIKAN LOGIKA SISA
            $remaining = max(0, $batch->target_quantity - $batch->current_quantity);
            if ($isLocked) {
                $remaining = 0;
            }

            $totalProduction = $batch->current_quantity + $batch->reject_quantity;
            $rejectRate = $totalProduction > 0 ? ($batch->reject_quantity / $totalProduction) * 100 : 0;
            $avgEff = $batch->reports->avg('efficiency') ?? 0;
            $totalDowntime = $batch->reports->sum('downtime_total');
            $isOverdue =
                $batch->deadline_date &&
                \Carbon\Carbon::now()
                    ->startOfDay()
                    ->gt(\Carbon\Carbon::parse($batch->deadline_date)) &&
                $batch->status != 'completed';
        @endphp
        <div class="batch-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 relative cursor-pointer group"
            onclick="window.openViewModal({{ $batch->toJson() }})">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h4
                        class="text-lg font-bold text-brand-600 dark:text-brand-400 group-hover:text-brand-700 transition-colors">
                        {{ $batch->batch_code }}</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($batch->start_date)->format('d M Y') }}
                        @if ($batch->deadline_date)
                            - <span
                                class="{{ $isOverdue ? 'text-rose-600 font-bold' : '' }}">{{ \Carbon\Carbon::parse($batch->deadline_date)->format('d M Y') }}
                                @if ($isOverdue)
                                    <i class="fas fa-exclamation-circle ml-1" title="Lewat Deadline!"></i>
                                @endif
                            </span>
                        @endif
                    </p>
                </div>
                <span
                    class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase border {{ $prioClass }}">{{ $batch->priority }}</span>
            </div>
            <div class="space-y-3 mb-4">
                <div>
                    <h5 class="text-sm font-bold text-slate-800 dark:text-white truncate"
                        title="{{ $batch->product->name ?? '-' }}">{{ $batch->product->name ?? '-' }}</h5>
                    <div class="flex items-center gap-2 mt-1">
                        <span
                            class="text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded">{{ $batch->color->name ?? '-' }}</span>
                        <span
                            class="text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded"><i
                                class="fas fa-robot mr-1"></i> {{ $batch->machine->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-end mb-1"><span
                            class="text-xs text-slate-500 dark:text-slate-400">Progress</span><span
                            class="text-xs font-bold text-brand-600 dark:text-brand-400">{{ $batch->progress ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                        <div class="bg-brand-500 h-2 rounded-full transition-all duration-500"
                            style="width: {{ $batch->progress ?? 0 }}%"></div>
                    </div>
                </div>

                {{-- DAFTAR METRIK YANG DISEDERHANAKAN --}}
                <div
                    class="grid grid-cols-2 gap-x-2 gap-y-2 text-xs border-t border-slate-100 dark:border-slate-700 pt-3">
                    <div class="flex justify-between"><span class="text-slate-500">Batch Size</span><span
                            class="font-bold text-slate-700 dark:text-slate-200">{{ number_format($batch->target_quantity, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">Total Produksi</span><span
                            class="font-bold text-slate-700 dark:text-slate-200">{{ number_format($totalProduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">FG (Good)</span><span
                            class="font-bold text-emerald-600">{{ number_format($fg, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">Reject</span>
                        <div class="text-right"><span
                                class="font-bold text-rose-500">{{ number_format($batch->reject_quantity, 0, ',', '.') }}</span><span
                                class="text-[9px] text-rose-400 ml-0.5">({{ number_format($rejectRate, 1) }}%)</span>
                        </div>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">Sisa Target</span><span
                            class="font-bold text-amber-600">{{ number_format($remaining, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">WIP</span><span
                            class="font-bold text-slate-700 dark:text-slate-300">{{ number_format($wip, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">Counter</span><span
                            class="font-bold text-indigo-500">{{ number_format($counter, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">Sample</span><span
                            class="font-bold text-blue-500">{{ number_format($totalSample, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">Downtime</span><span
                            class="font-bold text-amber-600">{{ number_format($totalDowntime, 0, ',', '.') }} m</span>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-500">Avg Eff</span><span
                            class="font-bold {{ $avgEff >= 90 ? 'text-emerald-600' : ($avgEff >= 70 ? 'text-amber-600' : 'text-rose-600') }}">{{ number_format($avgEff, 1) }}%</span>
                    </div>
                </div>
            </div>

            {{-- BOTTOM ACTION --}}
            <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-3 mt-auto"
                onclick="event.stopPropagation()">
                <div class="flex items-center gap-1 group-actions-{{ $batch->id }}">
                    <label class="relative inline-flex items-center cursor-pointer mr-2">
                        <input type="checkbox" id="toggle_{{ $batch->id }}" class="sr-only peer"
                            {{ $batch->is_active ? 'checked' : '' }}
                            onchange="updateActiveStatus({{ $batch->id }})">
                        <div
                            class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-brand-600">
                        </div>
                    </label>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $statusClass }}"
                        id="status-badge-{{ $batch->id }}">{{ $batch->status }}</span>
                </div>

                <div>
                    @if (in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <button onclick="window.openEditModal({{ $batch->toJson() }})"
                            class="btn-edit p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 dark:hover:text-amber-400 transition-colors"
                            title="Edit Batch" style="{{ $isLocked ? 'display:none;' : '' }}"><i
                                class="fas fa-pen"></i></button>
                        @if (!$isLocked)
                            <form action="{{ route('batches.destroy', $batch->id) }}" method="POST"
                                class="delete-form inline-block">@csrf @method('DELETE')<button type="button"
                                    onclick="confirmDelete(this)"
                                    class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 dark:hover:text-rose-400 transition-colors"
                                    title="Hapus Batch"><i class="fas fa-trash-alt"></i></button></form>
                        @endif
                        <button type="button" onclick="toggleLock({{ $batch->id }}, this)"
                            class="btn-lock p-2 rounded-lg text-slate-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700/50 dark:hover:text-gray-300 transition-colors"
                            title="{{ $isLocked ? 'Unlock (Ubah Status ke Running)' : 'Lock (Ubah Status ke Completed)' }}"><i
                                class="fas {{ $isLocked ? 'fa-lock' : 'fa-lock-open' }}"></i></button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-12 text-slate-500">
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-full p-4 mb-3"><i
                    class="fas fa-clipboard-list text-3xl text-slate-300 dark:text-slate-500"></i></div>
            <span class="font-medium">Belum ada data batch produksi.</span>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="mb-10">{{ $batches->links() }}</div>
