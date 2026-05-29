<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'keyword_id',
    'company_domain',
    'cooldown_hours',
    'max_applications',
    'period_hours',
])]
class CooldownRule extends Model
{
    protected function casts(): array
    {
        return [
            'cooldown_hours' => 'integer',
            'max_applications' => 'integer',
            'period_hours' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(JobKeyword::class, 'keyword_id');
    }
}
