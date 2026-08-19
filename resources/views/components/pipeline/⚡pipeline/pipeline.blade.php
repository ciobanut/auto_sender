<div class="space-y-6">
    {{-- Pipeline progress bar --}}
    <x-ui.card>
        <div class="flex items-center justify-between gap-2 overflow-x-auto px-2 pb-2">
            @php
                $stages = [
                    ['key' => 'fetch', 'label' => 'Fetch Jobs', 'icon' => 'tabler.download'],
                    ['key' => 'analyze', 'label' => 'Analyze', 'icon' => 'tabler.search'],
                    ['key' => 'generate', 'label' => 'Generate', 'icon' => 'tabler.messages'],
                    ['key' => 'review', 'label' => 'Review', 'icon' => 'tabler.eye'],
                    ['key' => 'send', 'label' => 'Send', 'icon' => 'tabler.send'],
                ];
                $currentIndex = array_search($this->stage, array_column($stages, 'key'));
            @endphp

            @foreach ($stages as $i => $stage)
                @php
                    $count = $this->stageCounts[$stage['key']] ?? 0;
                    $isActive = $this->stage === $stage['key'];
                    $isPast = $currentIndex > $i;
                @endphp

                <a href="{{ route('pipeline', ['stage' => $stage['key']]) }}"
                   class="flex flex-col items-center gap-2 rounded-lg px-4 py-3 transition-colors
                          {{ $isActive ? 'bg-primary/10 text-primary' : ($isPast ? 'text-muted-foreground' : 'text-muted-foreground/50 hover:text-muted-foreground') }}">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full border transition-colors
                                {{ $isActive ? 'border-primary bg-primary text-primary-foreground' : ($isPast ? 'border-primary bg-primary/10 text-primary' : 'border-border bg-background') }}">
                        @if($isPast)
                            <x-ui.icon name="tabler.check" class="h-5 w-5" />
                        @else
                            <x-ui.icon name="{{ $stage['icon'] }}" class="h-5 w-5" />
                        @endif
                    </div>
                    <span class="text-xs font-medium {{ $isActive ? 'font-semibold' : '' }}">{{ $stage['label'] }}</span>
                    @if($count > 0)
                        <x-ui.badge size="sm">{{ $count }}</x-ui.badge>
                    @endif
                </a>

                @if(!$loop->last)
                    <div class="mt-[-1rem] h-px flex-1 {{ $isPast ? 'bg-primary' : 'bg-border' }}"></div>
                @endif
            @endforeach
        </div>
    </x-ui.card>

    {{-- Stage content --}}
        @switch($this->stage)
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
