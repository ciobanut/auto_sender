<?php

use App\Models\JobDetail;
use App\Models\JobLink;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cover_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JobLink::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(JobDetail::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('keyword_id')->nullable()->constrained('job_keywords')->cascadeOnDelete();
            $table->longText('content');
            $table->integer('version')->default(1);
            $table->boolean('is_follow_up')->default(false);
            $table->string('ai_model')->nullable();
            $table->float('ai_confidence_score')->nullable();
            $table->text('match_explanation')->nullable();
            $table->json('extra_skills_injected')->nullable();
            $table->longText('editable_content')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->unique(['job_link_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cover_letters');
    }
};
