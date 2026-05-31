<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('app')->group(function () {
    Route::livewire('/', 'dashboard.dashboard')->name('dashboard');
    Route::livewire('/keywords', 'keywords.keywords')->name('keywords');
    Route::livewire('/cvs', 'cvs.cvs')->name('cvs');
    Route::livewire('/skills', 'skills.skills')->name('skills');
    Route::livewire('/ai-settings', 'ai-settings.ai-settings')->name('ai-settings');
    Route::livewire('/rules', 'rules.rules')->name('rules');
    Route::livewire('/analytics', 'analytics.analytics')->name('analytics');
    Route::livewire('/applications', 'applications.applications')->name('applications.log');
    Route::livewire('/pipeline/{stage?}', 'pipeline.pipeline')
        ->whereIn('stage', ['fetch', 'analyze', 'generate', 'review', 'send'])
        ->name('pipeline');
});
