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
        // get records from DB
        $products = Product::get();

        // check if there are records
        if ($products->count() > 0) {
            // return data in JsonResource format using ProductResource
            return ProductResource::collection($products);
        }
        else {
            // else, return a message in json format
            return response()->json(['message' => 'No records found'], 200);
        }
    }

    public function store(Request $request) {
        // validation
        $validated_input = request()->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'required',
                'price' => 'required|integer'
                ]
        );
        // add to database
        Product::create($validated_input);

        // return a confirmation message and the added data in json format
        return response()->json([
            'message' => 'Product Created Successfully',
            'data' => new ProductResource($validated_input)
        ], 200);
    }

    public function show(Product $product) {
        return new ProductResource($product) ;
    }

    public function update(Product $product) {
        // validation
        $validated_input = request()->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'required',
                'price' => 'required|integer'
                ]
        );

        // update product from database
        $product->update($validated_input);

        // return a confirmation message and the updated data in json format
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
