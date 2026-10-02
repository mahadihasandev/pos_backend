<?php

declare(strict_types=1);

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
        Schema::create('expenses', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('category', 64)->default('general'); // rent, utilities, salaries, maintenance, supplier_payment, other
            $table->string('title', 128);
            $table->decimal('amount', 12, 4);
            $table->string('payment_method', 32)->default('cash'); // cash, bank_transfer, card, mobile_money
            $table->string('reference_number', 64)->nullable();
            $table->date('incurred_at');
            $table->text('note')->nullable();
            $table->string('receipt_url', 512)->nullable();
            $table->timestamps();

            // Multi-tenant indexes for P&L and monthly expense reporting
            $table->index(['tenant_id', 'category', 'incurred_at'], 'idx_expenses_category_date');
            $table->index(['tenant_id', 'incurred_at'], 'idx_expenses_incurred_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
