<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('CV Manager') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('Upload and manage CV files per job category.') }}</p>
        </div>
    </div>

    @if($this->keywords->isEmpty())
    <div class="bg-base-100 rounded-xl border border-base-content/5 p-12 text-center">
        <x-ui.icon name="tabler.file-text" class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4 empty-icon" />
        <h3 class="text-lg font-medium mb-2">{{ __('No categories yet') }}</h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Add job categories first in the Keywords section.') }}</p>
    </div>
    @else
    <div class="bg-base-100 rounded-xl border border-base-content/5 overflow-hidden">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('CV File') }}</th>
                    <th>{{ __('Size') }}</th>
                    <th>{{ __('Updated') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->keywords as $keyword)
                <tr class="table-row-animate">
                    <td class="font-medium">{{ $keyword->keyword }}</td>
                    <td>
                        @if($keyword->cv_path && Storage::disk('cvs')->exists($keyword->cv_path))
                        <span class="badge badge-sm badge-success gap-1">
                            <x-ui.icon name="tabler.check" class="w-3 h-3" /> {{ basename($keyword->cv_path) }}
                        </span>
                        @else
                        <span class="badge badge-sm badge-ghost">{{ __('No file') }}</span>
                        @endif
                    </td>
                    <td class="text-sm text-zinc-500">
                        @if($keyword->cv_path && Storage::disk('cvs')->exists($keyword->cv_path))
                        {{ round(Storage::disk('cvs')->size($keyword->cv_path) / 1024, 1) }} KB
                        @else
                        —
                        @endif
                    </td>
                    <td class="text-sm text-zinc-500">
                        {{ $keyword->cv_path ? $keyword->updated_at->diffForHumans() : '—' }}
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            @if($keyword->cv_path && Storage::disk('cvs')->exists($keyword->cv_path))
                            <x-ui.button class="btn-ghost btn-xs" wire:click="download({{ $keyword->id }})">
                                <x-ui.icon name="tabler.download" class="w-3.5 h-3.5" />
                            </x-ui.button>
                            <x-ui.button class="btn-ghost btn-xs" wire:click="remove({{ $keyword->id }})" wire:confirm="{{ __('Remove CV?') }}">
                                <x-ui.icon name="tabler.trash" class="w-3.5 h-3.5" />
                            </x-ui.button>
                            @endif
                            <x-ui.button class="btn-ghost btn-xs" wire:click="startUpload({{ $keyword->id }})">
                                <x-ui.icon name="tabler.upload" class="w-3.5 h-3.5" /> {{ $keyword->cv_path ? __('Replace') : __('Upload') }}
                            </x-ui.button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Upload Modal --}}
    <x-modal wire:model="uploadingKeywordId" title="{{ __('Upload CV') }}">
        <div class="space-y-4">
            <p class="text-sm text-zinc-500">{{ __('Upload a CV file for this category. Supported formats: PDF, DOCX, TXT (max 2MB).') }}</p>
            <input type="file" wire:model="newCv" accept=".pdf,.docx,.txt" class="file-input file-input-bordered w-full text-sm" />
            @error('newCv') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            <div wire:loading wire:target="newCv" class="text-sm text-primary">{{ __('Uploading...') }}</div>
        </div>

        <x-slot:actions>
            <x-ui.button variant="ghost" wire:click="$set('uploadingKeywordId', null)">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button variant="default" wire:click="saveCv" wire:loading.attr="disabled">
                <x-ui.icon name="tabler.upload" class="w-4 h-4" /> {{ __('Upload') }}
            </x-ui.button>
        </x-slot:actions>
    </x-modal>
</div>
