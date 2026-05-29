<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyword_id')->constrained('job_keywords')->cascadeOnDelete();
            $table->string('job_url')->unique();
            $table->string('external_job_id')->nullable();
            $table->string('title');
            $table->string('company_name');
            $table->string('location')->nullable();
            $table->text('short_preview')->nullable();
            $table->string('status')->default('new');
            $table->integer('fetch_count')->default(1);
            $table->timestamp('first_seen_at')->useCurrent();
            $table->timestamp('re_fetched_at')->nullable();
            $table->timestamps();

            $table->index(['keyword_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_links');
    }
};
