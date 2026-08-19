<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('CV Manager') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('Upload and manage CV files per job category.') }}</p>
        </div>
    </div>

    @if($this->keywords->isEmpty())
    <x-ui.card>
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <x-ui.icon name="tabler.file-text" class="text-muted-foreground mb-3 h-12 w-12" />
            <h3 class="text-lg font-medium mb-2">{{ __('No categories yet') }}</h3>
            <p class="text-muted-foreground text-sm">{{ __('Add job categories first in the Keywords section.') }}</p>
        </div>
    </x-ui.card>
    @else
    <x-ui.card variant="sectioned">
        <x-ui.card-content>
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>{{ __('Category') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('CV File') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Size') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Updated') }}</x-ui.table-head>
                        <x-ui.table-head>{{ __('Actions') }}</x-ui.table-head>
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @foreach($this->keywords as $keyword)
                    <x-ui.table-row>
                        <x-ui.table-cell class="font-medium">{{ $keyword->keyword }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            @if($keyword->cv_path && Storage::disk('cvs')->exists($keyword->cv_path))
                            <x-ui.badge tone="success" variant="soft" class="gap-1">
                                <x-ui.icon name="tabler.check" class="h-3 w-3" /> {{ basename($keyword->cv_path) }}
                            </x-ui.badge>
                            @else
                            <x-ui.badge variant="soft">{{ __('No file') }}</x-ui.badge>
                            @endif
                        </x-ui.table-cell>
                        <x-ui.table-cell class="text-sm text-muted-foreground">
                            @if($keyword->cv_path && Storage::disk('cvs')->exists($keyword->cv_path))
                            {{ round(Storage::disk('cvs')->size($keyword->cv_path) / 1024, 1) }} KB
                            @else
                            —
                            @endif
                        </x-ui.table-cell>
                        <x-ui.table-cell class="text-sm text-muted-foreground">
                            {{ $keyword->cv_path ? $keyword->updated_at->diffForHumans() : '—' }}
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <div class="flex items-center gap-1">
                                @if($keyword->cv_path && Storage::disk('cvs')->exists($keyword->cv_path))
                                <x-ui.button variant="ghost" size="icon-xs" wire:click="download({{ $keyword->id }})">
                                    <x-ui.icon name="tabler.download" class="h-3.5 w-3.5" />
                                </x-ui.button>
                                @endif
                                <x-ui.button variant="ghost" size="icon-xs" wire:click="upload({{ $keyword->id }})">
                                    <x-ui.icon name="tabler.upload" class="h-3.5 w-3.5" />
                                </x-ui.button>
                                @if($keyword->cv_path && Storage::disk('cvs')->exists($keyword->cv_path))
                                <x-ui.button variant="ghost" size="icon-xs" class="text-destructive" wire:click="deleteCv({{ $keyword->id }})" wire:confirm="{{ __('Delete this CV?') }}">
                                    <x-ui.icon name="tabler.trash" class="h-3.5 w-3.5" />
                                </x-ui.button>
                                @endif
                            </div>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                    @endforeach
                </x-ui.table-body>
            </x-ui.table>
        </x-ui.card-content>
    </x-ui.card>
    @endif

    {{-- Upload Modal --}}
    <x-modal wire:model="uploadingKeywordId" title="{{ __('Upload CV') }}">
        <div class="space-y-4">
            <p class="text-sm text-muted-foreground">{{ __('Upload a CV file for this category. Supported formats: PDF, DOCX, TXT (max 2MB).') }}</p>
            <input type="file" wire:model="newCv" accept=".pdf,.docx,.txt" class="w-full text-sm rounded-md border border-input bg-background px-3 py-2 file:mr-3 file:rounded-md file:border-0 file:bg-primary file:text-primary-foreground file:text-sm file:font-medium file:px-3 file:py-1 hover:file:bg-primary/90" />
            @error('newCv') <p class="text-xs text-destructive mt-1">{{ $message }}</p> @enderror
            <div wire:loading wire:target="newCv" class="text-sm text-primary">{{ __('Uploading...') }}</div>
        </div>

        <x-slot:actions>
            <x-ui.button variant="ghost" wire:click="$set('uploadingKeywordId', null)">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button wire:click="saveCv" wire:loading.attr="disabled">
                <x-ui.icon name="tabler.upload" class="h-4 w-4" /> {{ __('Upload') }}
            </x-ui.button>
        </x-slot:actions>
    </x-modal>
</div>
