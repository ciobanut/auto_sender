<?php

namespace App\Livewire\Cvs;

use App\Models\JobKeyword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

#[Title('CV Manager')]
class Index extends Component
{
    use Toast, WithFileUploads;

    public ?int $uploadingKeywordId = null;

    public $newCv;

    public function startUpload(int $keywordId): void
    {
        $this->uploadingKeywordId = $keywordId;
        $this->newCv = null;
    }

    public function saveCv(): void
    {
        $this->validate(['newCv' => 'required|file|mimes:pdf,docx,txt|max:2048']);

        $keyword = JobKeyword::whereUserId(Auth::id())->findOrFail($this->uploadingKeywordId);

        if ($keyword->cv_path) {
            Storage::disk('cvs')->delete($keyword->cv_path);
        }

        $keyword->update(['cv_path' => $this->newCv->store('', 'cvs')]);

        $this->success(__('CV uploaded.'));
        $this->reset(['uploadingKeywordId', 'newCv']);
    }

    public function download(int $id): void
    {
        $keyword = JobKeyword::whereUserId(Auth::id())->findOrFail($id);

        if ($keyword->cv_path && Storage::disk('cvs')->exists($keyword->cv_path)) {
            $this->dispatch('download-cv', path: Storage::disk('cvs')->path($keyword->cv_path));
        }
    }

    public function remove(int $id): void
    {
        $keyword = JobKeyword::whereUserId(Auth::id())->findOrFail($id);

        if ($keyword->cv_path) {
            Storage::disk('cvs')->delete($keyword->cv_path);
            $keyword->update(['cv_path' => null]);
        }

        $this->success(__('CV removed.'));
    }

    #[Computed]
    public function keywords()
    {
        return JobKeyword::whereUserId(Auth::id())
            ->orderBy('sort_order')
            ->get();
    }

    public function render()
    {
        return view('livewire.cvs.index')
            ->layout('layouts.app', ['title' => 'CV Manager']);
    }
}
