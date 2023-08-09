<?php

namespace App\Http\Controllers;

use App\Jobs\ProductLiked;
use App\Models\Product;
use App\Models\ProductUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function like($id, Request $request)
    {
        $user = Http::get('http://docker.for.mac.localhost:8000/api/user/random')->json();

        try {
            $productUser = ProductUser::create([
                'product_id' => $id,
                'user_id' => $user['id'],
            ]);

            ProductLiked::dispatch($productUser->toArray())->onQueue('admin_queue');

            return response()->json([
                'message' => 'Product liked',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Product already liked',
            ], 400);
        }
    }
}
