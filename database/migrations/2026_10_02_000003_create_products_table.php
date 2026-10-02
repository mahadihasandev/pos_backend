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
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name', 255);
            $table->string('sku', 64);
            $table->string('barcode', 64)->index();
            $table->decimal('cost_price', 12, 4)->default(0.0000);
            $table->decimal('selling_price', 12, 4)->default(0.0000);
            $table->decimal('stock_quantity', 12, 4)->default(0.0000);
            $table->decimal('min_stock_alert', 12, 4)->default(5.0000);
            $table->string('unit', 16)->default('pcs'); // pcs, kg, g, l, pack
            $table->boolean('is_weight_variable')->default(false); // digital scale weight barcodes (e.g. EAN-13 price/weight embedded)
            $table->decimal('tax_rate', 6, 4)->default(0.0000); // 0.0500 = 5% VAT
            $table->string('image_url', 512)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Multi-tenant indexes for lightning-fast barcode scans & searches
            $table->unique(['tenant_id', 'barcode'], 'uq_products_tenant_barcode');
            $table->unique(['tenant_id', 'sku'], 'uq_products_tenant_sku');
            $table->index(['tenant_id', 'category_id', 'is_active'], 'idx_products_tenant_category');
            $table->index(['tenant_id', 'is_active', 'stock_quantity'], 'idx_products_tenant_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
