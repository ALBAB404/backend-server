<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class shopController extends Controller
{
    public function index(Request $request)
    {
        try {

            $sort = $request->sort;
            $per_page_data = $request->show;
            $brandIds = $request->brand;
            $categoryIds = $request->category;
            $price_range = $request->price_range;
            $search = $request->search;

            $products = Product::latest()->published()
                ->when($sort != 'default', function ($q) use ($sort) {
                    return $q->conditions($sort);
                })
                ->when($brandIds, function ($q) use ($brandIds) {
                    return $q->whereIn('brand_id', $brandIds);
                })
                ->when($categoryIds, function ($q) use ($categoryIds) {
                    return $q->whereIn('category_id', $categoryIds);
                })
                ->when($search, function ($q) use ($search) {
                    return $q->where('name', 'LIKE', '%' . $search . '%');
                })
                ->when($price_range, function ($q) use ($price_range) {

                    $min_price = $price_range[0];
                    $max_price = $price_range[1];

                    return $q->whereBetween('price', [$min_price, $max_price]);
                })
                ->paginate($per_page_data);

            return ProductResource::collection($products);

        } catch (\Exception $e) {
            return send_ms($e->getMessage(), false, $e->getCode());
        }
    }

    public function sideBar()
    {
        try {

            $categories = Category::withCount('products')->status('active')->get();
            $brands = Brand::withCount('products')->status('active')->get();

            $min_price = Product::min('price');
            $max_price = Product::max('price');

            return ProductResource::collection([
                'categories' => $categories,
                'brands' => $brands,
                'price' => [
                    'min_price' => $min_price,
                    'max_price' => $max_price,
                ],
            ]);

        } catch (\Exception $e) {
            return send_ms($e->getMessage(), false, $e->getCode());
        }
    }
}
