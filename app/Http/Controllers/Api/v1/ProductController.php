<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of active supermarket products for the POS terminal.
     */
    public function index(Request $request): JsonResponse
    {
        $tenantId = (int) $request->header('X-Tenant-Id', 1);

        $products = Product::forTenant($tenantId)
            ->active()
            ->with('category:id,name,slug,icon')
            ->when($request->filled('category_id'), function ($q) use ($request): void {
                $q->where('category_id', (int) $request->query('category_id'));
            })
            ->when($request->filled('search'), function ($q) use ($request): void {
                $search = (string) $request->query('search');
                $q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        $categories = Category::forTenant($tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon']);

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'products' => $products,
            ],
        ]);
    }
}
