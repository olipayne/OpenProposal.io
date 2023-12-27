<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('applicant_info', 2000);
            $table->string('publication_title', 200);
            $table->json('proposed_authors');
            $table->longText('study_background');
            $table->longText('research_question');
            $table->longText('data_and_population');
            $table->longText('analysis_plan');
            $table->date('start_analysis_date');
            $table->date('start_writing_date');
            $table->date('completion_date');
            $table->string('status', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
