<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter quantity column to numeric(12, 4) to support weighed produce (kg/grams) and decimal sales
        DB::statement('ALTER TABLE order_items ALTER COLUMN quantity TYPE numeric(12, 4) USING quantity::numeric(12, 4)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE order_items ALTER COLUMN quantity TYPE integer USING quantity::integer');
    }
};
