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
        Schema::create('guardians', function (Blueprint $table) {

            //First stage of Data

            $table->id();
            $table->foreignId('user_id')->constrained('users');

            //Second stage of data

            $table->string('profile_photo')->nullable(); 
            $table->enum('gender', ['FEMALE', 'MALE']);
            $table->string('date_of_birth');
            $table->enum('marital_status', ['SINGLE', 'MARRIED', 'COMPLICATED']);
            $table->string('phone_number');          
            $table->string('alt_phn_number');
            $table->string('home_address');
            $table->string('state_of_origin');
            $table->string('local_government_area');
           
            //Third stage Occupation

            $table->enum('employment_status', ['EMPLOYED', 'UNEMPLOYED', 'SELF_EMPLOYED']);
            $table->string('nature_of_occupation');
            $table->bigInteger('annual_income');
            $table->string('employer_name');
            $table->string('employer_phone');
            $table->string('employer_address');


            // Fourth Stage Identity
            $table->enum('mean_of_identity', ['NATIONAL_ID', 'VOTERS_CARD', 'DRIVER_LICENCE', 'INTERNATIONAL_PASSWORD']);
            $table->bigInteger('identity_number');
         
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
