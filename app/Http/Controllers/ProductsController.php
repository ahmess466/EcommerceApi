<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);

        if ($products->count()) {
            return response()->json($products, 200);
        } else {
            return response()->json(['message' => 'No products found'], 404);
        }
    }

    public function show($id)
    {
        $product = Product::find($id);

        if ($product) {
            return response()->json($product, 200);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->discount = $request->discount;
        $product->amount = $request->amount;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;

            try {
                $file->move(public_path("assets/uploads/product/"), $filename);
            } catch (FileException $e) {
                return response()->json(['message' => 'Failed to upload image'], 500);
            }

            $product->image = $filename;
        }

        $product->save();

        return response()->json(['message' => 'Product added successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->discount = $request->discount;
        $product->amount = $request->amount;

        if ($request->hasFile('image')) {
            $path = public_path("assets/uploads/product/") . $product->image;
            if ($product->image && File::exists($path)) {
                File::delete($path);
            }

            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;

            try {
                $file->move(public_path("assets/uploads/product/"), $filename);
            } catch (FileException $e) {
                return response()->json(['message' => 'Failed to upload image'], 500);
            }

            $product->image = $filename;
        }

        $product->save();

        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    public function delete($id)
    {
        $product = Product::find($id);

        if ($product) {
            $path = public_path("assets/uploads/product/") . $product->image;
            if ($product->image && File::exists($path)) {
                File::delete($path);
            }

            $product->delete();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
