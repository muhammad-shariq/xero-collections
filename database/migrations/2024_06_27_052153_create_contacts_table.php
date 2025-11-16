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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('ref_contact_id', 64);
            $table->string('name');
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('mobile_number');
            $table->string('contact_status');
            $table->decimal('ar_outstanding');
            $table->decimal('ar_overdue');
            $table->json('json_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
