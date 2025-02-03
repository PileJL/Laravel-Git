<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        $products = Product::get();

        // check if there are records
        if ($products) {
            return ProductResource::collection($products);
        }
        else {
            return response()->json(['message' => 'No records found'], 200);
        }
    }

    public function store() {

    }

    public function show() {

    }

    public function update() {

    }

    public function destroy() {

    }
}
