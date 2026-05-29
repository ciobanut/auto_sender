<?php

namespace App\Models;

use Database\Factories\JobKeywordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'keyword',
    'cv_path',
    'ai_instructions',
    'auto_apply_enabled',
    'cooldown_hours',
    'is_active',
    'sort_order',
])]
class JobKeyword extends Model
{
    /** @use HasFactory<JobKeywordFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'auto_apply_enabled' => 'boolean',
            'is_active' => 'boolean',
            'cooldown_hours' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobLinks(): HasMany
    {
        return $this->hasMany(JobLink::class);
    }

    public function coverLetters(): HasMany
    {
        return $this->hasMany(CoverLetter::class, 'keyword_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'keyword_id');
    }

    public function cooldownRules(): HasMany
    {
        return $this->hasMany(CooldownRule::class, 'keyword_id');
    }
}
