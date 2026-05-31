<?php

use App\Models\ExtraSkill;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] class extends Component
{
    use Toast;

    public bool $showForm = false;

    #[Rule('required|string|max:50')]
    public string $name = '';

    #[Rule('required|string|in:backend,frontend,devops,other')]
    public string $category = 'other';

    #[Rule('required|string|in:beginner,intermediate,advanced')]
    public string $proficiency = 'intermediate';

    #[Computed]
    public function skills()
    {
        return ExtraSkill::whereUserId(Auth::id())
            ->orderBy('sort_order')
            ->get();
    }

    public function add(): void
    {
        $this->validate();

        ExtraSkill::create([
            'user_id' => Auth::id(),
            'name' => $this->name,
            'category' => $this->category,
            'proficiency' => $this->proficiency,
            'sort_order' => ExtraSkill::whereUserId(Auth::id())->max('sort_order') + 1,
        ]);

        $this->success(__('Skill added.'));
        $this->reset(['name', 'category', 'proficiency']);
        $this->showForm = false;
    }

    public function remove(int $id): void
    {
        ExtraSkill::whereUserId(Auth::id())->whereId($id)->delete();
        $this->success(__('Skill removed.'));
    }
};
