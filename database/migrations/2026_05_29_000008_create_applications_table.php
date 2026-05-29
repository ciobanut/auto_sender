<?php

use App\Models\CoverLetter;
use App\Models\JobLink;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JobLink::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(CoverLetter::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('keyword_id')->nullable()->constrained('job_keywords')->cascadeOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->string('delivery_status')->default('pending');
            $table->boolean('response_received')->default(false);
            $table->timestamp('response_at')->nullable();
            $table->string('response_type')->nullable();
            $table->text('recruiter_reply_text')->nullable();
            $table->boolean('follow_up_sent')->default(false);
            $table->timestamp('follow_up_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['delivery_status', 'response_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
