<?php

namespace App\Models;

use Database\Factories\CoverLetterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'job_link_id',
    'job_detail_id',
    'keyword_id',
    'content',
    'version',
    'is_follow_up',
    'ai_model',
    'ai_confidence_score',
    'match_explanation',
    'extra_skills_injected',
    'editable_content',
    'status',
])]
class CoverLetter extends Model
{
    /** @use HasFactory<CoverLetterFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'is_follow_up' => 'boolean',
            'ai_confidence_score' => 'float',
            'extra_skills_injected' => 'array',
        ];
    }

    public function jobLink(): BelongsTo
    {
        return $this->belongsTo(JobLink::class);
    }

    public function jobDetail(): BelongsTo
    {
        return $this->belongsTo(JobDetail::class);
    }

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(JobKeyword::class, 'keyword_id');
    }
}
