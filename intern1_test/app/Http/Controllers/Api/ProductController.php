<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index() {
        $products = Product::get();

        // check if there are records
        if ($products->countBy() > 0) {
            return ProductResource::collection($products);
        }
        else {
            return response()->json(['message' => 'No records found'], 200);
        }
    }

    public function store(Request $request) {
        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required',
            'price' => 'required|integer'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => "All fields are mandatory",
                'error' => $validator->messages()
            ], 422);
        }
        // add to database
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price
        ]);

        return response()->json([
            'message' => 'Product Created Successfully',
            'data' => new ProductResource($product)
        ], 200);
    }

    public function show(Product $product) {
        // add validation here
        return new ProductResource($product) ;
    }

    public function update(Request $request, Product $product) {
        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required',
            'price' => 'required|integer'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => "All fields are mandatory",
                'error' => $validator->messages()
            ], 422);
        }
        // update product from database
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price
        ]);

        return response()->json([
            'message' => 'Product Updated Successfully',
            'data' => new ProductResource($product)
        ], 200);
    }

    public function destroy(Product $product) {
        $product->delete();

        return response()->json([
            'message' => 'Product Deleted Successfully',
        ], 200);
    }
}
