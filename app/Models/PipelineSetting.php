<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'fetch_concurrent',
    'analyze_concurrent',
    'generate_concurrent',
    'send_concurrent',
])]
class PipelineSetting extends Model
{
    protected function casts(): array
    {
        return [
            'fetch_concurrent' => 'integer',
            'analyze_concurrent' => 'integer',
            'generate_concurrent' => 'integer',
            'send_concurrent' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
