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
            $table->tinyInteger('orphan_id');
            $table->tinyInteger('guardian_id');

            $table->string('profile_photo')->nullable(); 
            $table->enum('gender', ['FEMALE', 'MALE']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('guidian_affidavit')->nullable();
            $table->string('state_of_origin');
            $table->string('local_government');
            $table->string('date_of_birth');


            $table->enum('in_school', ['YES','NO']);
            $table->string('school_name');
            $table->string('school_address');
            $table->string('school_contact_person');
            $table->string('phone_number_of_contact_person');
            $table->string('class');


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
