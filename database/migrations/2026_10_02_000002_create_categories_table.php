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
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->string('name', 128);
            $table->string('slug', 128);
            $table->text('description')->nullable();
            $table->string('icon', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Multi-tenant indexes
            $table->unique(['tenant_id', 'slug'], 'uq_categories_tenant_slug');
            $table->index(['tenant_id', 'is_active'], 'idx_categories_tenant_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
