<?php

namespace App\Models;

use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'job_link_id',
    'cover_letter_id',
    'keyword_id',
    'sent_at',
    'delivery_status',
    'response_received',
    'response_at',
    'response_type',
    'recruiter_reply_text',
    'follow_up_sent',
    'follow_up_at',
    'notes',
])]
class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'response_received' => 'boolean',
            'response_at' => 'datetime',
            'follow_up_sent' => 'boolean',
            'follow_up_at' => 'datetime',
        ];
    }

    public function jobLink(): BelongsTo
    {
        return $this->belongsTo(JobLink::class);
    }

    public function coverLetter(): BelongsTo
    {
        return $this->belongsTo(CoverLetter::class);
    }

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(JobKeyword::class, 'keyword_id');
    }
}
