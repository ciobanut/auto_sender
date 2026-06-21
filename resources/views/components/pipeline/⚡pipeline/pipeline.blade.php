<div class="space-y-6">
    {{-- Pipeline progress bar --}}
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-5">
        <ul class="steps w-full">

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
            $isActive = $this->stage === $stage['key'];
            $isPast = array_search($this->stage, array_column($stages, 'key')) > $i;
            @endphp
            <a href="{{ route('pipeline', ['stage' => $stage['key']]) }}" class="
            step cursor-pointer transition-colors
            {{ $isActive ? 'step-primary font-semibold' : '' }}
            {{ $isPast ? 'step-primary' : '' }}
            ">
                <span class="step-icon">
                    <x-icon name="{{ $stage['icon'] }}" class="w-4 h-4" />
                </span>
                {{ $stage['label'] }}
                @if($count > 0)
                <span class="text-xs badge badge-sm mt-1">{{ $count }}</span>
                @endif
            </a>
            @endforeach
        </ul>
    </div>

    {{-- Stage content --}}
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-5">
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
</div>
