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
        Schema::create('shifts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('terminal_code', 32)->default('POS-01');
            $table->string('status', 16)->default('open'); // open, closed
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_float', 12, 4)->default(0.0000);
            $table->decimal('closing_cash_counted', 12, 4)->default(0.0000);
            $table->decimal('system_expected_cash', 12, 4)->default(0.0000);
            $table->decimal('cash_difference', 12, 4)->default(0.0000); // counted - expected (over/short variance)
            $table->decimal('total_sales_amount', 12, 4)->default(0.0000);
            $table->unsignedInteger('total_orders_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Multi-tenant indexes for active shift checks & historical audits
            $table->index(['tenant_id', 'user_id', 'status'], 'idx_shifts_tenant_user_status');
            $table->index(['tenant_id', 'opened_at'], 'idx_shifts_tenant_opened_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
