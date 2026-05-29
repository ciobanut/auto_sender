<?php

namespace App\Models;

use Database\Factories\JobLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'job_keyword_id',
    'job_url',
    'external_job_id',
    'title',
    'company_name',
    'location',
    'short_preview',
    'status',
    'fetch_count',
    'first_seen_at',
    're_fetched_at',
])]
class JobLink extends Model
{
    /** @use HasFactory<JobLinkFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'fetch_count' => 'integer',
            'first_seen_at' => 'datetime',
            're_fetched_at' => 'datetime',
        ];
    }

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(JobKeyword::class, 'job_keyword_id');
    }

    public function detail(): HasOne
    {
        return $this->hasOne(JobDetail::class);
    }

    public function coverLetters(): HasMany
    {
        return $this->hasMany(CoverLetter::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
