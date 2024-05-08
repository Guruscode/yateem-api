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
        Schema::create('orphan_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->nullable()->constrained('guardians')->onDelete('cascade');
            $table->foreignId('orphan_id')->nullable()->constrained('orphans')->onDelete('cascade');
            $table->enum('activity', ['EDUCATION', 'HEALTH', 'CLOTHING', 'FEEDING'])->nullable();
            $table->text('description')->nullable();
            $table->string('upload_document')->nullable();
            $table->string('insert_link')->nullable();
            $table->string('name_of_health_facility')->nullable();
            $table->string('card_id_number')->nullable();
            $table->string('type_of_disease')->nullable();
            $table->string('feeding_program')->nullable();
            $table->string('feeding_formula')->nullable();
            $table->string('size_of_shirt')->nullable();
            $table->string('size_of_trouser')->nullable();
            $table->string('arms_length')->nullable();
            $table->string('name_of_school_contact_person')->nullable();
            $table->string('phone_number')->nullable();
            $table->date('date_of_enrollment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orphan_activities');
    }
};
