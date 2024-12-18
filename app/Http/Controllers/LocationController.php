<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{

    public function store (Request $request){
        $request->validate([

            'street' =>'required',
            'city' =>'required',
            'building' => 'required'
        ]);
        $location = new Location();
        $location->street = $request->street;
        $location->city = $request->city;
        $location->building = $request->building;
        $location->user_id = Auth::id();
        $location->save();
        return response()->json('Location Added',201);
    }

    public function update(Request $request,$id){
        $request -> validate([
            'street' =>'required',
          'city' =>'required',
          'building' => 'required'
          ]);
          $location = Location::find($id);
          if($location){
          $location->street = $request->street;
          $location->city = $request->city;
          $location->building = $request->building;
          $location->save();
          return response()->json('Location Updated Succesfully',200);}
          else{
            return response()->json('Location Not Found',404);
          }



    }
    public function delete($id){
        $location = Location::find($id);
        if($location){
            $location->delete();
        return response()->json('Location Deleted Succesfully',200);

        }
        else{
            return response()->json('Location Not Found',404);
        }

    }
}
