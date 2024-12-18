<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Faker\Core\File;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::paginate(10);
        return response()->json($categories,200);
    }
    public function show($id){
        $category = Category::find($id);
        if ($category){
            return response()->json($category,200);
        }
        else{
            return response()->json("Category Not Found");


        }
    }
    public function store(Request $request)
{
    try {
        // Validate request
        $validated = $request->validate([
            'name' => 'required|unique:categories,name',
            'image' => 'required|mimes:jpeg,png,jpg|max:2048'
        ]);

        $category = new Category();

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = uniqid() . '.' . $ext;

            // Store file securely
            $path = public_path('assets/uploads/category');
            if (!file_exists($path)) {
                mkdir($path, 0755, true); // Create directory if not exists
            }

            $file->move($path, $filename);

            $category->image = $filename;
        }

        // Save category
        $category->name = $request->name;
        $category->save();

        return response()->json($category, 201);

    } catch (\Exception $e) {
        // Log error and return generic message
        return response()->json(['error' => 'Something went wrong while saving the category.'], 500);
    }
}

public function update(Request $request, $id)
{
    try {
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Find category
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            $oldImagePath = public_path('assets/uploads/category/' . $category->image);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            // Upload new image
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = uniqid() . '.' . $ext;
            $file->move(public_path('assets/uploads/category'), $filename);

            $category->image = $filename;
        }

        // Update category name
        $category->name = $request->name;
        $category->save();

        return response()->json(['message' => 'Category updated successfully'], 200);
    } catch (\Exception $e) {
        // Log the error and return a generic message
        return response()->json(['error' => 'Something went wrong while updating the category.'], 500);
    }
}

    public function delete($id){
        try {
            $category = Category::find($id);
        $category->delete();
        return response()->json('Category Deleted Successfully',200);
        }
        catch (\Exception $th) {
            return response()->json($th,500);
        }



    }
}
