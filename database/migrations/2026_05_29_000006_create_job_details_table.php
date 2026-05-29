<?php

use App\Models\JobLink;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JobLink::class)->unique()->constrained()->cascadeOnDelete();
            $table->longText('full_description')->nullable();
            $table->json('technologies')->nullable();
            $table->integer('salary_from')->nullable();
            $table->integer('salary_to')->nullable();
            $table->string('salary_currency')->nullable();
            $table->string('company_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('recruiter_name')->nullable();
            $table->string('phone')->nullable();
            $table->json('requirements')->nullable();
            $table->json('responsibilities')->nullable();
            $table->string('seniority')->nullable();
            $table->string('work_type')->nullable();
            $table->timestamp('publication_date')->nullable();
            $table->boolean('reposted')->default(false);
            $table->integer('repost_count')->default(0);
            $table->integer('reposted_after_days')->nullable();
            $table->string('similarity_hash')->nullable();
            $table->float('similarity_score')->nullable();
            $table->timestamps();

            $table->index('similarity_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_details');
    }
};
