<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brands::paginate(10);
        return response()->json($brands, 200);
    }

    public function show($id)
    {
        $brand = Brands::find($id);
        if ($brand) {
            return response()->json($brand, 200);
            } else {
                return response()->json(['message' => 'Brand not found'], 404);
            }                
    }
    public function store( Request $request)
    {
        try{
            $validated = $request->validate([
                'name'=>'required|unique:brands,name',
            ]);
            $brand = new Brands();
            $brand->name = $request->name;
            $brand->save();
            return response()->json(['message' => 'Brand Added'], 201);

        }catch(Exception $e) {
            return response()->json($e, 500);
        }
          
    }

    public function update_brand($id, Request $request)
    {
        try{
            $validated = $request->validate([
                'name'=>'required|unique:brands,name',
            ]);
            $brand = Brands::where('id', $id)->update(['name' => $request->name]);
            return response()->json(['message' => 'Brand Updated'], 200);
        }catch(Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function delete_brand($id)
    { 
        $brand = Brands::find($id);
            if($brand){
                $brand->delete();
                    return response()->json(['message' => 'Brand Deleted'], 201);
            }else{
                return response()->json(['message' => 'Brand not found'], 404);
            }
    
    }
}
