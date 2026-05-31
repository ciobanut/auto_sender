<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('app')->group(function () {
    Route::livewire('/', 'dashboard.index')->name('dashboard');
    Route::livewire('/keywords', 'keywords.index')->name('keywords');
    Route::livewire('/cvs', 'cvs.index')->name('cvs');
    Route::livewire('/skills', 'skills.index')->name('skills');
    Route::livewire('/ai-settings', 'ai-settings.index')->name('ai-settings');
    Route::livewire('/rules', 'rules.index')->name('rules');
    Route::livewire('/analytics', 'analytics.index')->name('analytics');
    Route::livewire('/applications', 'applications.index')->name('applications.log');
    Route::livewire('/pipeline/{stage?}', 'pipeline.index')
        ->whereIn('stage', ['fetch', 'analyze', 'generate', 'review', 'send'])
        ->name('pipeline');
});
