<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-semibold">{{ __('Send Applications') }}</h3>
            <p class="text-xs text-zinc-500">{{ __('Send approved applications with CV and cover letter.') }}</p>
        </div>
        <x-button variant="primary" wire:click="send" wire:loading.attr="disabled" :disabled="$this->approvedLetters->isEmpty()">
            @if($isSending)
                <span class="loading loading-spinner loading-sm"></span>
            @else
                <x-icon name="tabler.send" class="w-4 h-4" />
            @endif
            {{ __('Send Applications') }}
        </x-button>
    </div>

    {{-- Ready to send --}}
    @if($this->approvedLetters->isNotEmpty())
        <div class="bg-base-100 rounded-xl border border-emerald-200 dark:border-emerald-800 p-4">
            <div class="flex items-center gap-2 mb-3">
                <x-icon name="tabler.send" class="w-4 h-4 text-emerald-500" />
                <h4 class="text-sm font-medium">{{ __('Ready to Send') }} ({{ $this->approvedLetters->count() }})</h4>
            </div>
            <div class="space-y-2">
                @foreach($this->approvedLetters as $letter)
                    <div class="flex items-center justify-between text-sm py-1">
                        <div class="flex items-center gap-2">
                            <span class="font-medium">{{ $letter->jobLink?->title }}</span>
                            <span class="text-zinc-500 text-xs">{{ $letter->jobLink?->company_name }}</span>
                        </div>
                        <span class="badge badge-sm badge-success">{{ __('Approved') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Sent applications --}}
    @if($this->sentApplications->isNotEmpty())
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                <h4 class="text-sm font-medium">{{ __('Sent Applications') }} ({{ $this->sentApplications->count() }})</h4>
            </div>
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>{{ __('Job') }}</th>
                        <th>{{ __('Company') }}</th>
                        <th>{{ __('Sent') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Response') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->sentApplications as $app)
                        <tr>
                            <td class="font-medium text-sm max-w-xs truncate">{{ $app->jobLink?->title ?? '—' }}</td>
                            <td class="text-sm">{{ $app->jobLink?->company_name ?? '—' }}</td>
                            <td class="text-sm text-zinc-500">{{ $app->sent_at?->diffForHumans() ?? '—' }}</td>
                            <td>
                                <span class="badge badge-sm
                                    {{ $app->delivery_status === 'delivered' ? 'badge-success' : '' }}
                                    {{ $app->delivery_status === 'pending' ? 'badge-ghost' : '' }}
                                    {{ $app->delivery_status === 'failed' ? 'badge-error' : '' }}
                                    {{ $app->delivery_status === 'bounced' ? 'badge-warning' : '' }}">
                                    {{ $app->delivery_status }}
                                </span>
                            </td>
                            <td>
                                @if($app->response_received)
                                    <span class="badge badge-sm
                                        {{ $app->response_type === 'interview' ? 'badge-success' : '' }}
                                        {{ $app->response_type === 'rejected' ? 'badge-error' : '' }}
                                        {{ $app->response_type === 'no_reply' ? 'badge-ghost' : '' }}">
                                        {{ $app->response_type }}
                                    </span>
                                @else
                                    <span class="text-xs text-zinc-400">{{ __('—') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($this->approvedLetters->isEmpty() && $this->sentApplications->isEmpty())
        <div class="bg-base-100 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center">
            <x-icon name="tabler.send" class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
            <p class="text-sm text-zinc-500">{{ __('No applications to send. Approve messages in the Review stage first.') }}</p>
        </div>
    @endif
</div>
