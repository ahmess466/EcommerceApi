<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(){
        $brands = Brand::paginate(10);
        return response()->json($brands,200);
    }
    public function show($id){
        $brand = Brand::find($id);
        if ($brand){
            return response()->json($brand,200);
        }
        else{
            return response()->json("Brand Not Found");


        }
    }
    public function store(Request $request){
        try {
            $validated = $request->validate([
                'name' => 'required|unique:brands,name',
            ]);
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->save();
            return response()->json($brand,201);
        } catch (\Exception $th) {
            return response()->json($th,500);
        }
    }
    public function update(Request $request,$id){
        try {
            $validated = $request->validate([
                'name' => 'required',
            ]);
            $brand = Brand::where('id',$id)->update(['name'=>$request->name]);
            return response()->json('Brand Updated Successfully',200);
        }
        catch (\Exception $th) {
            return response()->json($th,500);


        }
    }
    public function delete($id){
        try {
            $brand = Brand::find($id);
        $brand->delete();
        return response()->json('Brand Deleted Successfully',200);
        }
        catch (\Exception $th) {
            return response()->json($th,500);
        }



    }
}
