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
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->string('name', 128);
            $table->string('email', 128);
            $table->string('password');
            $table->string('role', 32)->default('cashier'); // super_admin, branch_manager, floor_supervisor, cashier
            $table->string('pin_code', 16)->nullable(); // Fast supervisor/manager PIN authorization for POS terminal
            $table->string('phone', 32)->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            // Multi-tenant indexes
            $table->unique(['tenant_id', 'email'], 'uq_users_tenant_email');
            $table->index(['tenant_id', 'role', 'is_active'], 'idx_users_tenant_role_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
