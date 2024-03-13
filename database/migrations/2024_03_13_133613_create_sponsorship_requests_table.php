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
        Schema::create('sponsorship_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->constrained('guardians');
            $table->foreignId('orphan_id')->constrained('orphans');
            $table->enum('need', ['EDUCATION', 'HEALTH', 'CLOTHING','FEEDING'])->nullable;
            $table->text('description')->nullable();
            $table->decimal('amount_needed', 10, 2);
            $table->decimal('current_amount', 10, 2)->default(0);
            $table->enum('request_status', ['PENDING', 'APPROVED', 'REJECTED',]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorship_requests');
    }
};
