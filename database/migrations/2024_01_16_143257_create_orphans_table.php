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
        Schema::create('orphans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardians_id')->constrained()->onDelete('cascade');
            $table->string('profile_photo')->nullable(); 
            $table->enum('gender', ['FEMALE', 'MALE']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('guidian_affidavit')->nullable();
            $table->string('state_of_origin');
            $table->string('local_government');
            $table->date('date_of_birth');
            $table->enum('in_school', ['YES', 'NO']);
            $table->string('school_name')->nullable();
            $table->string('school_address')->nullable();
            $table->string('school_contact_person')->nullable();
            $table->string('phone_number_of_contact_person')->nullable();
            $table->enum('account_status', ['PENDING', 'APPROVED', 'REJECTED',]);
            $table->string('unique_code');
            $table->string('class')->nullable();
            $table->enum('delete_request_status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->timestamps();
 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orphans');
    }
};
