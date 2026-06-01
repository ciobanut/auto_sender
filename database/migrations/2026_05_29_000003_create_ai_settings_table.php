<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->unique()->constrained()->cascadeOnDelete();
            $table->string('model')->default('deepseek-v4-flash');
            $table->float('temperature')->default(0.7);
            $table->integer('max_tokens')->default(500);
            $table->string('language')->default('ro');
            $table->string('tone')->default('professional');
            $table->text('signature_block')->nullable();
            $table->text('default_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
