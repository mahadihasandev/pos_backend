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
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('order_number', 64);
            $table->string('status', 32)->default('pending');
            $table->decimal('subtotal', 12, 4)->default(0.0000);
            $table->decimal('tax_amount', 12, 4)->default(0.0000);
            $table->decimal('discount_amount', 12, 4)->default(0.0000);
            $table->decimal('total_amount', 12, 4)->default(0.0000);
            $table->string('payment_method', 32)->default('card');
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Composite indexes for high-throughput queries
            $table->index(['tenant_id', 'status', 'created_at'], 'idx_orders_tenant_status_date');
            $table->unique(['tenant_id', 'order_number'], 'uq_orders_tenant_order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
