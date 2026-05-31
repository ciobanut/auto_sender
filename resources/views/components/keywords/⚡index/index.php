<?php

use App\Models\JobKeyword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new #[Title('Job Categories')] #[Layout('layouts.app')] class extends Component
{
    use Toast, WithFileUploads;

    public bool $showForm = false;

    public ?int $editingKeywordId = null;

    #[Rule('required|string|max:50')]
    public string $keyword = '';

    #[Rule('nullable|file|mimes:pdf,docx,txt|max:2048')]
    public $cv;

    #[Rule('nullable|string|max:2000')]
    public string $ai_instructions = '';

    #[Rule('boolean')]
    public bool $auto_apply_enabled = false;

    #[Rule('integer|min:1|max:8760')]
    public int $cooldown_hours = 720;

    #[Rule('boolean')]
    public bool $is_active = true;

    #[Computed]
    public function keywords()
    {
        return JobKeyword::whereUserId(Auth::id())
            ->orderBy('sort_order')
            ->get();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingKeywordId = null;
    }

    public function edit(int $id): void
    {
        $keyword = JobKeyword::whereUserId(Auth::id())->findOrFail($id);

        $this->editingKeywordId = $keyword->id;
        $this->keyword = $keyword->keyword;
        $this->ai_instructions = $keyword->ai_instructions ?? '';
        $this->auto_apply_enabled = $keyword->auto_apply_enabled;
        $this->cooldown_hours = $keyword->cooldown_hours;
        $this->is_active = $keyword->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'keyword' => $this->keyword,
            'ai_instructions' => $this->ai_instructions ?: null,
            'auto_apply_enabled' => $this->auto_apply_enabled,
            'cooldown_hours' => $this->cooldown_hours,
            'is_active' => $this->is_active,
        ];

        if ($this->editingKeywordId) {
            $keyword = JobKeyword::whereUserId(Auth::id())->findOrFail($this->editingKeywordId);

            if ($this->cv) {
                if ($keyword->cv_path) {
                    Storage::disk('cvs')->delete($keyword->cv_path);
                }
                $data['cv_path'] = $this->cv->store('', 'cvs');
            }

            $keyword->update($data);
            $this->success(__('Keyword updated.'));
        } else {
            $data['user_id'] = Auth::id();
            $data['sort_order'] = JobKeyword::whereUserId(Auth::id())->max('sort_order') + 1;

            if ($this->cv) {
                $data['cv_path'] = $this->cv->store('', 'cvs');
            }

            JobKeyword::create($data);
            $this->success(__('Keyword created.'));
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $keyword = JobKeyword::whereUserId(Auth::id())->findOrFail($id);
        $keyword->update(['is_active' => ! $keyword->is_active]);
    }

    public function delete(int $id): void
    {
        $keyword = JobKeyword::whereUserId(Auth::id())->findOrFail($id);

        if ($keyword->cv_path) {
            Storage::disk('cvs')->delete($keyword->cv_path);
        }

        $keyword->delete();
        $this->success(__('Keyword deleted.'));
    }

    public function moveUp(int $id): void
    {
        $keywords = $this->keywords;
        $current = $keywords->firstWhere('id', $id);
        $previous = $keywords->firstWhere('sort_order', $current->sort_order - 1);

        if ($previous) {
            $current->update(['sort_order' => $current->sort_order - 1]);
            $previous->update(['sort_order' => $previous->sort_order + 1]);
        }
    }

    public function moveDown(int $id): void
    {
        $keywords = $this->keywords;
        $current = $keywords->firstWhere('id', $id);
        $next = $keywords->firstWhere('sort_order', $current->sort_order + 1);

        if ($next) {
            $current->update(['sort_order' => $current->sort_order + 1]);
            $next->update(['sort_order' => $next->sort_order - 1]);
        }
    }

    private function resetForm(): void
    {
        $this->reset(['keyword', 'cv', 'ai_instructions', 'auto_apply_enabled', 'cooldown_hours', 'is_active', 'editingKeywordId']);
    }
};
