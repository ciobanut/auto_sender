<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Analytics') }}</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Track your job application performance and AI effectiveness.') }}</p>
    </div>

    {{-- Stats grid --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                    <x-icon name="tabler.briefcase" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Keywords') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stats['total_keywords'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="tabler.send" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Applications') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stats['total_applications'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                    <x-icon name="tabler.message-reply" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Reply Rate') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stats['reply_rate'] }}%</p>
                </div>
            </div>
        </div>
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                    <x-icon name="tabler.star" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Interview Rate') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stats['interview_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Top keywords --}}
    @if(count($this->topKeywords) > 0)
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
            <h2 class="font-semibold mb-4">{{ __('Top Keywords') }}</h2>
            <div class="space-y-3">
                @foreach($this->topKeywords as $kw)
                    <div class="flex items-center justify-between">
                        <span class="font-medium">{{ $kw['keyword'] }}</span>
                        <span class="badge badge-sm">{{ $kw['applications_count'] }} {{ __('applications') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
