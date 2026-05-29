<div class="space-y-6">
    {{-- Welcome header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('Recruitment Dashboard') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Manage your automated job applications pipeline.') }}</p>
        </div>
    </div>

    {{-- Pipeline progress bar --}}
    <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
        <ul class="steps steps-horizontal w-full">
            @php
            $stages = [
                ['key' => 'fetch', 'label' => 'Fetch Jobs', 'icon' => 'tabler.download'],
                ['key' => 'analyze', 'label' => 'Analyze', 'icon' => 'tabler.search'],
                ['key' => 'generate', 'label' => 'Generate', 'icon' => 'tabler.messages'],
                ['key' => 'review', 'label' => 'Review', 'icon' => 'tabler.eye'],
                ['key' => 'send', 'label' => 'Send', 'icon' => 'tabler.send'],
            ];
            @endphp

            @foreach ($stages as $i => $stage)
                @php
                    $count = $this->stageCounts[$stage['key']] ?? 0;
                    $isActive = $this->activeStage === $stage['key'];
                    $isPast = array_search($this->activeStage, array_column($stages, 'key')) > $i;
                @endphp
                <li class="step cursor-pointer transition-colors
                    {{ $isActive ? 'step-primary font-semibold' : '' }}
                    {{ $isPast ? 'step-neutral opacity-60' : '' }}"
                    wire:click="setStage('{{ $stage['key'] }}')">
                    <div class="flex flex-col items-center">
                        <span>{{ $stage['label'] }}</span>
                        @if($count > 0)
                            <span class="text-xs badge badge-sm mt-1">{{ $count }}</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Stats row --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                    <x-icon name="tabler.briefcase" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total Jobs') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stageCounts['fetch'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="tabler.messages" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('AI Drafts') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stageCounts['generate'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                    <x-icon name="tabler.eye" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Needs Review') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stageCounts['review'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                    <x-icon name="tabler.send" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Pending Send') }}</p>
                    <p class="text-2xl font-bold">{{ $this->stageCounts['send'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stage content --}}
    <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5">
        @switch($activeStage)
            @case('fetch')
                @livewire('pipeline.fetch-jobs', key('fetch'))
                @break
            @case('analyze')
                @livewire('pipeline.analyze-jobs', key('analyze'))
                @break
            @case('generate')
                @livewire('pipeline.generate-messages', key('generate'))
                @break
            @case('review')
                @livewire('pipeline.review-applications', key('review'))
                @break
            @case('send')
                @livewire('pipeline.send-applications', key('send'))
                @break
        @endswitch
    </div>
</div>
