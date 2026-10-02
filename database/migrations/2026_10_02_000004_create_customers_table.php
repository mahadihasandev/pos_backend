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
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->string('name', 128);
            $table->string('phone', 32)->index();
            $table->string('email', 128)->nullable();
            $table->string('address', 255)->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->decimal('credit_balance', 12, 4)->default(0.0000); // Current due amount (Khata/Credit ledger)
            $table->decimal('credit_limit', 12, 4)->default(0.0000); // Max allowed credit (0 = no credit allowed)
            $table->decimal('total_spent', 12, 4)->default(0.0000);
            $table->unsignedInteger('orders_count')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Multi-tenant indexes for customer lookup & credit monitoring
            $table->unique(['tenant_id', 'phone'], 'uq_customers_tenant_phone');
            $table->index(['tenant_id', 'credit_balance'], 'idx_customers_tenant_credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
