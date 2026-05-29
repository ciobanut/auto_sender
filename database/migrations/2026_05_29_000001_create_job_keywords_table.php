<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('keyword');
            $table->string('cv_path')->nullable();
            $table->text('ai_instructions')->nullable();
            $table->boolean('auto_apply_enabled')->default(false);
            $table->integer('cooldown_hours')->default(720);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'keyword']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_keywords');
    }
};
