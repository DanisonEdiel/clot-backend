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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('lastDigits')->nullable();
            $table->string('clientTransactionId')->nullable();
            $table->string('transactionId')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->string('email')->nullable();
            $table->string('cardType')->nullable();
            $table->string('transactionStatus')->nullable();
            $table->string('authorizationCode');
            $table->float('amount');
            $table->date('date');
            $table->foreignId('plan_id')->references('id')->on('plans');
            $table->foreignUuid('tenant_id')->references('id')->on('tenants');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
