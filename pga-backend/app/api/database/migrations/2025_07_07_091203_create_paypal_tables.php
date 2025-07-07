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
        // 1. Payers Table
        Schema::create('paypal_payers', function (Blueprint $table) {
            $table->id();
            $table->string('paypal_account_id')->unique();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('status')->nullable(); // VERIFIED, UNVERIFIED, etc.
            $table->timestamps();
        });

        // 2. Amounts Table
        Schema::create('paypal_amounts', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 10)->default('USD');
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('paypal_fee', 10, 2)->nullable();
            $table->decimal('net_amount', 10, 2)->nullable();
            $table->decimal('receivable_amount', 10, 2)->nullable();
            $table->decimal('exchange_rate', 12, 10)->nullable();
            $table->string('source_currency', 10)->nullable();
            $table->timestamps();
        });

        // 3. Main Payment Table
        Schema::create('paypal_payments', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('payer_id')->constrained('paypal_payers')->onDelete('cascade');
            $table->foreignId('amount_id')->constrained('paypal_amounts')->onDelete('cascade');

            // PayPal specific
            $table->string('order_id')->unique();
            $table->string('capture_id')->nullable();
            $table->enum('status', ['CREATED', 'APPROVED', 'COMPLETED', 'FAILED'])->default('CREATED');
            $table->boolean('is_sandbox')->default(true);

            $table->timestamps();
        });

        // 4. Payment Logs Table
        Schema::create('paypal_payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paypal_payment_id')->constrained('paypal_payments')->onDelete('cascade');

            $table->enum('type', ['CREATE_ORDER', 'CAPTURE_ORDER', 'WEBHOOK'])->default('WEBHOOK');
            $table->json('payload');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paypal_payment_logs');
        Schema::dropIfExists('paypal_payments');
        Schema::dropIfExists('paypal_amounts');
        Schema::dropIfExists('paypal_payers');
    }
};
