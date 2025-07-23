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
        Schema::create('form_multiple_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('to');
            $table->string('cc')->nullable();
            $table->string('subject');
            $table->json('attachments');
            $table->enum('status_sent', ['Pending', 'Delivered', 'Re-Send'])->default('pending');
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_multiple_uploads');
    }
};
