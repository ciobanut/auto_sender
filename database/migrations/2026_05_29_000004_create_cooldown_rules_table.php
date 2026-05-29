<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooldown_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignId('keyword_id')->nullable()->constrained('job_keywords')->cascadeOnDelete();
            $table->string('company_domain')->nullable();
            $table->integer('cooldown_hours');
            $table->integer('max_applications')->default(1);
            $table->integer('period_hours')->default(720);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooldown_rules');
    }
};
