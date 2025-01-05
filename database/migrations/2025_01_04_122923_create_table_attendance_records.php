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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id('attendance_record_id');
            $table->foreignId('class_id')->constrained('classes', 'class_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('attendance_date');
            $table->json('present_students')->nullable();
            $table->softDeletes();
            $table->timestamps();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
