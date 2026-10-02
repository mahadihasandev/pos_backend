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
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('sku', 64)->index();
            $table->string('product_name', 255);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 4)->default(0.0000);
            $table->decimal('total_price', 12, 4)->default(0.0000);
            $table->timestamps();

            // Composite index for fast order item lookups
            $table->index(['order_id', 'sku'], 'idx_order_items_order_sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
