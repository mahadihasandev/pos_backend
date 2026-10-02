<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tenantId = 1;

        // 1. Seed Manager & Cashier Users
        $manager = User::firstOrCreate(
            ['tenant_id' => $tenantId, 'email' => 'admin@supershop.com'],
            [
                'name' => 'Store Manager',
                'password' => Hash::make('password123'),
                'role' => 'branch_manager',
                'pin_code' => '1234',
                'phone' => '01711111111',
                'is_active' => true,
            ]
        );

        $supervisor = User::firstOrCreate(
            ['tenant_id' => $tenantId, 'email' => 'supervisor@supershop.com'],
            [
                'name' => 'Floor Supervisor',
                'password' => Hash::make('password123'),
                'role' => 'floor_supervisor',
                'pin_code' => '9999',
                'phone' => '01722222222',
                'is_active' => true,
            ]
        );

        $cashier = User::firstOrCreate(
            ['tenant_id' => $tenantId, 'email' => 'cashier@supershop.com'],
            [
                'name' => 'Terminal Cashier 1',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
                'pin_code' => '0000',
                'phone' => '01733333333',
                'is_active' => true,
            ]
        );

        // 2. Seed Standard Supermarket Categories
        $categories = [
            ['name' => 'Dairy & Bakery', 'slug' => 'dairy-bakery', 'icon' => 'milk'],
            ['name' => 'Fresh Produce', 'slug' => 'fresh-produce', 'icon' => 'apple'],
            ['name' => 'Beverages', 'slug' => 'beverages', 'icon' => 'cup-soda'],
            ['name' => 'Snacks & Sweets', 'slug' => 'snacks-sweets', 'icon' => 'cookie'],
            ['name' => 'Household & Hygiene', 'slug' => 'household-hygiene', 'icon' => 'sparkles'],
        ];

        $categoryMap = [];
        foreach ($categories as $cat) {
            $created = Category::firstOrCreate(
                ['tenant_id' => $tenantId, 'slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'is_active' => true,
                ]
            );
            $categoryMap[$cat['slug']] = $created->id;
        }

        // 3. Seed Realistic Retail Products with High Quality Images & Barcodes
        $products = [
            [
                'category_id' => $categoryMap['dairy-bakery'] ?? null,
                'name' => 'Full Cream Fresh Milk 1L',
                'sku' => 'DAIRY-MLK-01',
                'barcode' => '8901030383445',
                'cost_price' => 75.0000,
                'selling_price' => 90.0000,
                'stock_quantity' => 150.0000,
                'min_stock_alert' => 20.0000,
                'unit' => 'pcs',
                'is_weight_variable' => false,
                'tax_rate' => 0.0000,
                'image_url' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['dairy-bakery'] ?? null,
                'name' => 'Artisan Sliced Bread 400g',
                'sku' => 'BAKE-BRD-01',
                'barcode' => '8901234567890',
                'cost_price' => 50.0000,
                'selling_price' => 65.0000,
                'stock_quantity' => 80.0000,
                'min_stock_alert' => 15.0000,
                'unit' => 'pcs',
                'is_weight_variable' => false,
                'tax_rate' => 0.0000,
                'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['dairy-bakery'] ?? null,
                'name' => 'Farm Fresh Brown Eggs (12 pcs)',
                'sku' => 'DAIRY-EGG-12',
                'barcode' => '8901099887766',
                'cost_price' => 130.0000,
                'selling_price' => 160.0000,
                'stock_quantity' => 60.0000,
                'min_stock_alert' => 10.0000,
                'unit' => 'box',
                'is_weight_variable' => false,
                'tax_rate' => 0.0000,
                'image_url' => 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['fresh-produce'] ?? null,
                'name' => 'Fresh Cavendish Bananas',
                'sku' => 'PROD-BAN-01',
                'barcode' => '2000010000000',
                'cost_price' => 80.0000,
                'selling_price' => 120.0000,
                'stock_quantity' => 100.0000,
                'min_stock_alert' => 10.0000,
                'unit' => 'kg',
                'is_weight_variable' => true,
                'tax_rate' => 0.0000,
                'image_url' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['fresh-produce'] ?? null,
                'name' => 'Crisp Red Fuji Apples',
                'sku' => 'PROD-APL-01',
                'barcode' => '2000020000000',
                'cost_price' => 220.0000,
                'selling_price' => 280.0000,
                'stock_quantity' => 75.0000,
                'min_stock_alert' => 10.0000,
                'unit' => 'kg',
                'is_weight_variable' => true,
                'tax_rate' => 0.0000,
                'image_url' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['fresh-produce'] ?? null,
                'name' => 'Organic Fresh Tomatoes 1kg',
                'sku' => 'PROD-TOM-01',
                'barcode' => '2000030000000',
                'cost_price' => 50.0000,
                'selling_price' => 70.0000,
                'stock_quantity' => 90.0000,
                'min_stock_alert' => 15.0000,
                'unit' => 'kg',
                'is_weight_variable' => true,
                'tax_rate' => 0.0000,
                'image_url' => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['beverages'] ?? null,
                'name' => 'Coca-Cola Zero Sugar 500ml',
                'sku' => 'BEV-COC-01',
                'barcode' => '0123456789012',
                'cost_price' => 35.0000,
                'selling_price' => 45.0000,
                'stock_quantity' => 200.0000,
                'min_stock_alert' => 24.0000,
                'unit' => 'pcs',
                'is_weight_variable' => false,
                'tax_rate' => 0.0500,
                'image_url' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['beverages'] ?? null,
                'name' => 'Fresh Valencia Orange Juice 1L',
                'sku' => 'BEV-ORJ-01',
                'barcode' => '8901044332211',
                'cost_price' => 140.0000,
                'selling_price' => 190.0000,
                'stock_quantity' => 50.0000,
                'min_stock_alert' => 12.0000,
                'unit' => 'pcs',
                'is_weight_variable' => false,
                'tax_rate' => 0.0500,
                'image_url' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['beverages'] ?? null,
                'name' => 'Natural Spring Water 1.5L',
                'sku' => 'BEV-WTR-01',
                'barcode' => '8901066554433',
                'cost_price' => 20.0000,
                'selling_price' => 30.0000,
                'stock_quantity' => 300.0000,
                'min_stock_alert' => 50.0000,
                'unit' => 'pcs',
                'is_weight_variable' => false,
                'tax_rate' => 0.0000,
                'image_url' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['snacks-sweets'] ?? null,
                'name' => 'Crispy Salted Potato Chips 50g',
                'sku' => 'SNK-CHP-01',
                'barcode' => '8901058852336',
                'cost_price' => 20.0000,
                'selling_price' => 30.0000,
                'stock_quantity' => 120.0000,
                'min_stock_alert' => 20.0000,
                'unit' => 'pcs',
                'is_weight_variable' => false,
                'tax_rate' => 0.0500,
                'image_url' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['snacks-sweets'] ?? null,
                'name' => 'Rich Dark Chocolate Bar 100g',
                'sku' => 'SNK-DRK-01',
                'barcode' => '8901077665544',
                'cost_price' => 160.0000,
                'selling_price' => 220.0000,
                'stock_quantity' => 85.0000,
                'min_stock_alert' => 15.0000,
                'unit' => 'pcs',
                'is_weight_variable' => false,
                'tax_rate' => 0.0500,
                'image_url' => 'https://images.unsplash.com/photo-1606312619070-d48b4c652a52?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['household-hygiene'] ?? null,
                'name' => 'Antibacterial Hand Wash 250ml',
                'sku' => 'HYG-HND-01',
                'barcode' => '8901088452101',
                'cost_price' => 110.0000,
                'selling_price' => 150.0000,
                'stock_quantity' => 45.0000,
                'min_stock_alert' => 10.0000,
                'unit' => 'pcs',
                'is_weight_variable' => false,
                'tax_rate' => 0.0500,
                'image_url' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'category_id' => $categoryMap['household-hygiene'] ?? null,
                'name' => 'Citrus Dishwashing Liquid 500ml',
                'sku' => 'HYG-DSH-01',
                'barcode' => '8901011223344',
                'cost_price' => 90.0000,
                'selling_price' => 125.0000,
                'stock_quantity' => 60.0000,
                'min_stock_alert' => 10.0000,
                'unit' => 'pcs',
                'is_weight_variable' => false,
                'tax_rate' => 0.0500,
                'image_url' => 'https://images.unsplash.com/photo-1585670210693-e7fdd16b142e?auto=format&fit=crop&w=400&q=80',
            ],
        ];

        foreach ($products as $prod) {
            Product::updateOrCreate(
                ['tenant_id' => $tenantId, 'barcode' => $prod['barcode']],
                array_merge($prod, ['tenant_id' => $tenantId, 'is_active' => true])
            );
        }

        // 4. Seed Standard Customers (Walk-in & Loyalty Khata)
        Customer::updateOrCreate(
            ['tenant_id' => $tenantId, 'phone' => '01700000000'],
            [
                'name' => 'Walk-in Customer',
                'email' => 'walkin@supershop.com',
                'loyalty_points' => 0,
                'credit_balance' => 0.0000,
                'credit_limit' => 0.0000,
                'is_active' => true,
            ]
        );

        Customer::updateOrCreate(
            ['tenant_id' => $tenantId, 'phone' => '01812345678'],
            [
                'name' => 'Rafiqul Islam (VIP)',
                'email' => 'rafiq@example.com',
                'loyalty_points' => 120,
                'credit_balance' => 450.0000,
                'credit_limit' => 5000.0000,
                'is_active' => true,
            ]
        );
    }
}
