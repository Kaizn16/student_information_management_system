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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id('subject_id')->unique();
            $table->foreignId('strand_id')->nullable()->constrained('strands', 'strand_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('subject_code', 50);
            $table->string('subject_title',100);
            $table->text('subject_description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
