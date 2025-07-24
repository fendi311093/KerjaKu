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
        Schema::create('form_multiple_upload_cc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_multiple_upload_id')->constrained('form_multiple_uploads')->cascadeOnDelete();
            $table->foreignId('email_address_id')->constrained('email_addresses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_multiple_upload_cc');
    }
};