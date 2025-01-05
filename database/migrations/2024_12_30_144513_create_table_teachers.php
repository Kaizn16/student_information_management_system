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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id('teacher_id');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->cascadeOnDelete('set null')->cascadeOnUpdate('set null');
            $table->string('faculty_uid');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->bigInteger('age');
            $table->enum('sex', ['Male', 'Female']);
            $table->string('civil_status');
            $table->date('date_of_birth');
            $table->string('nationality');
            $table->string('contact_no')->nullable();
            $table->string('email')->unique();
            $table->foreignId('region_id')->constrained('regions', 'region_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('province_id')->constrained('provinces', 'province_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('municipality_id')->constrained('municipalities', 'municipality_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('barangay_id')->constrained('barangays', 'barangay_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('street_address')->nullable();
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_relation');
            $table->string('emergency_contact_no');
            $table->string('tin_id')->unique();
            $table->string('sss_number')->unique();
            $table->string('pagibig_number')->unique();
            $table->string('philhealth_number')->unique();
            $table->string('prc_license_number')->unique();
            $table->date('prc_license_expiration_date');
            $table->string('highest_degree');
            $table->string('field_of_specialiation');
            $table->string('university_graduated_name');
            $table->string('year_graduated');
            $table->string('additional_course_training')->nullable();
            $table->enum('designation', ['Adviser', 'Teacher']);
            $table->json('subjects_handle')->nullable();
            $table->enum('employment_type', ['Part-Time', 'Full-Time']);
            $table->date('date_hired');
            $table->enum('employment_status', ['Active', 'Inactive']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
