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
        Schema::table('orders', function (Blueprint $table): void {
            $table->unsignedBigInteger('shift_id')->nullable()->after('customer_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->after('shift_id')->index(); // Cashier ID
            $table->decimal('paid_amount', 12, 4)->default(0.0000)->after('total_amount');
            $table->decimal('change_amount', 12, 4)->default(0.0000)->after('paid_amount');
            $table->integer('loyalty_points_earned')->default(0)->after('change_amount');
            $table->integer('loyalty_points_redeemed')->default(0)->after('loyalty_points_earned');
            $table->boolean('is_credit_sale')->default(false)->after('loyalty_points_redeemed'); // Customer Due / Khata
            $table->decimal('due_amount', 12, 4)->default(0.0000)->after('is_credit_sale');

            $table->index(['tenant_id', 'shift_id'], 'idx_orders_tenant_shift');
            $table->index(['tenant_id', 'user_id', 'created_at'], 'idx_orders_cashier_date');
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->decimal('cost_price', 12, 4)->default(0.0000)->after('unit_price'); // For COGS and Profit Margin calculation
            $table->decimal('discount_amount', 12, 4)->default(0.0000)->after('cost_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn(['cost_price', 'discount_amount']);
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex('idx_orders_tenant_shift');
            $table->dropIndex('idx_orders_cashier_date');
            $table->dropColumn([
                'shift_id',
                'user_id',
                'paid_amount',
                'change_amount',
                'loyalty_points_earned',
                'loyalty_points_redeemed',
                'is_credit_sale',
                'due_amount',
            ]);
        });
    }
};
