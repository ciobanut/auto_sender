<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'model',
    'temperature',
    'max_tokens',
    'language',
    'tone',
    'signature_block',
    'default_instructions',
])]
class AiSetting extends Model
{
    protected function casts(): array
    {
        return [
            'temperature' => 'float',
            'max_tokens' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
