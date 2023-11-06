<?php

namespace App\Http\Controllers\api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\SellerResource;
use App\Models\Seller;
use Illuminate\Http\Request;

class sellerListController extends Controller
{
    public function index(Request $request)
    {
        $seller = Seller::VerifiedSeller()->withCount('products')->latest()->paginate($request->show);
        // dd($seller);
        return SellerResource::collection($seller);
    }

    public function sellerProducts(Seller $slug)
    {
        try {
            return new SellerResource($slug) ;
            // return response()->json($slug);
        } catch (\Exception $e) {
            return send_ms($e->getMessage(), false, $e->getCode());
        }
    }
}
