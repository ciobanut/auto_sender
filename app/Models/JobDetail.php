<?php

namespace App\Models;

use Database\Factories\JobDetailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'job_link_id',
    'full_description',
    'technologies',
    'salary_from',
    'salary_to',
    'salary_currency',
    'company_name',
    'contact_email',
    'recruiter_name',
    'phone',
    'requirements',
    'responsibilities',
    'seniority',
    'work_type',
    'publication_date',
    'reposted',
    'repost_count',
    'reposted_after_days',
    'similarity_hash',
    'similarity_score',
])]
class JobDetail extends Model
{
    /** @use HasFactory<JobDetailFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'requirements' => 'array',
            'responsibilities' => 'array',
            'salary_from' => 'integer',
            'salary_to' => 'integer',
            'reposted' => 'boolean',
            'repost_count' => 'integer',
            'reposted_after_days' => 'integer',
            'similarity_score' => 'float',
            'publication_date' => 'datetime',
        ];
    }

    public function jobLink(): BelongsTo
    {
        return $this->belongsTo(JobLink::class);
    }
}
