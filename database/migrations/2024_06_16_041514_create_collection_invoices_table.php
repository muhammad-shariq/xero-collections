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
        Schema::create('collection_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('invoice_id', 64)->unique();
            $table->string('contact_id', 64);
            $table->string('contact_email');
            $table->string('contact_name');
            $table->string('invoice_number')->nullable();
            $table->string('reference')->nullable();
            $table->decimal('amount_due');
            $table->decimal('amount_paid');
            $table->unsignedTinyInteger('email_sent')->nullable();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('last_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_invoices');
    }
};
