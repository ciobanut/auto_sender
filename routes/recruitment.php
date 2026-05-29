<?php

use App\Livewire\AiSettings\Index as AiSettings;
use App\Livewire\Analytics\Index as Analytics;
use App\Livewire\Applications\Index as Applications;
use App\Livewire\Cvs\Index as Cvs;
use App\Livewire\Dashboard\Index as Dashboard;
use App\Livewire\Keywords\Index as Keywords;
use App\Livewire\Pipeline\Index as Pipeline;
use App\Livewire\Rules\Index as Rules;
use App\Livewire\Skills\Index as Skills;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('app')->group(function () {
    Route::livewire('/', Dashboard::class)->name('dashboard');
    Route::livewire('/keywords', Keywords::class)->name('keywords');
    Route::livewire('/cvs', Cvs::class)->name('cvs');
    Route::livewire('/skills', Skills::class)->name('skills');
    Route::livewire('/ai-settings', AiSettings::class)->name('ai-settings');
    Route::livewire('/rules', Rules::class)->name('rules');
    Route::livewire('/analytics', Analytics::class)->name('analytics');
    Route::livewire('/applications', Applications::class)->name('applications.log');
    Route::livewire('/pipeline/{stage?}', Pipeline::class)
        ->whereIn('stage', ['fetch', 'analyze', 'generate', 'review', 'send'])
        ->name('pipeline');
});
