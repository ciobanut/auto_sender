<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('Application Log') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Track all your sent applications and responses.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <select wire:model.live="filter" class="select select-sm select-bordered">
                <option value="">{{ __('All') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="delivered">{{ __('Delivered') }}</option>
                <option value="failed">{{ __('Failed') }}</option>
                <option value="bounced">{{ __('Bounced') }}</option>
            </select>
        </div>
    </div>

    @if($applications->isEmpty())
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-12 text-center">
        <x-icon name="tabler.history" class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
        <h3 class="text-lg font-medium mb-2">{{ __('No applications yet') }}</h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Applications will appear here once you start sending them.') }}</p>
    </div>
    @else
    <div class="bg-base-100 rounded-xl border border-base-content/5 overflow-hidden">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>{{ __('Job') }}</th>
                    <th>{{ __('Company') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Sent') }}</th>
                    <th>{{ __('Response') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $app)
                <tr>
                    <td class="font-medium">{{ $app->jobLink?->title ?? '—' }}</td>
                    <td>{{ $app->jobLink?->company_name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-sm
                                    {{ $app->delivery_status === 'delivered' ? 'badge-success' : '' }}
                                    {{ $app->delivery_status === 'failed' ? 'badge-error' : '' }}
                                    {{ $app->delivery_status === 'pending' ? 'badge-ghost' : '' }}
                                    {{ $app->delivery_status === 'bounced' ? 'badge-warning' : '' }}">
                            {{ $app->delivery_status }}
                        </span>
                    </td>
                    <td class="text-sm text-zinc-500">{{ $app->sent_at?->diffForHumans() ?? '—' }}</td>
                    <td>
                        @if($app->response_received)
                        <span class="badge badge-sm
                                        {{ $app->response_type === 'interview' ? 'badge-success' : '' }}
                                        {{ $app->response_type === 'rejected' ? 'badge-error' : '' }}">
                            {{ $app->response_type }}
                        </span>
                        @else
                        <span class="text-sm text-zinc-400">{{ __('Awaiting') }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
