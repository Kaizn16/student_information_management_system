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
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->cascadeOnDelete('set null')->cascadeOnUpdate('set null');
            $table->string('student_uid')->unique();
            $table->string('lrn')->unique();
            $table->string('first_name');   
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->enum('sex', ['Male', 'Female']);
            $table->bigInteger('age');
            $table->date('date_of_birth');
            $table->string('place_of_birth');
            $table->string('nationality');
            $table->foreignId('region_id')->constrained('regions', 'region_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('province_id')->constrained('provinces', 'province_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('municipality_id')->constrained('municipalities', 'municipality_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('barangay_id')->constrained('barangays', 'barangay_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('street_address')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('email')->unique();
            $table->string('father_first_name')->nullable();
            $table->string('father_middle_name')->nullable();
            $table->string('father_last_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_contact_no')->nullable();
            $table->string('mother_first_name')->nullable();
            $table->string('mother_middle_name')->nullable();
            $table->string('mother_last_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_contact_no')->nullable();
            $table->string('guardian_first_name')->nullable();
            $table->string('guardian_middle_name')->nullable();
            $table->string('guardian_last_name')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->string('guardian_contact_no')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('previous_school_name');
            $table->string('birth_certificate')->nullable();
            $table->foreignId('teacher_id')->constrained('teachers', 'teacher_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('report_card')->nullable();
            $table->string('current_year_level');
            $table->foreignId('strand_id')->constrained('strands', 'strand_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('section');
            $table->string('school_year');
            $table->enum('enrollment_status', ['Stopped', 'Continuing', 'Graduated']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
