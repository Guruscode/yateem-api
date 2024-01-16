<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;

class CategoryController extends Controller
{
    //
    public function index()
    {
        $categories = Categories::paginate(10);
        return response()->json($categories, 200);
    }

    public function show($id)
    {
        $categories = Categories::find($id);
        if ($categories) {
            return response()->json($categories, 200);
            } else {
                return response()->json(['message' => 'Categories not found'], 404);
            }                
    }
    public function store( Request $request)
    {
        try{
            $validated = $request->validate([
                'name'=>'required|unique:categories,name',
                'image'=>'required',
            ]);
            $categories = new Categories();
            $categories->name = $request->name;
            $categories->save();
            return response()->json(['message' => 'Categories Added'], 201);

        }catch(Exception $e) {
            return response()->json($e, 500);
        }
          
    }

    public function update_categories($id, Request $request)
    {
        try{
            $validated = $request->validate([
                'name'=>'required|unique:categories,name',
                'image'=>'required',
            ]);
            $categories = Categories::find($id);
            if ($request->hasFile('image')){
                $path = 'assets/uploads/category/'. $categories->image;
                if(File::exists($path)){
                    File::delete($path);
                }
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $filename = time(). '.'.$ext;
                try{
                    $file->move('assets/uploads/category', $filename);
                }catch(FileException $e){
                    dd($e);
                }
                $categories->image = $filename;
            }
            $categories = Categories::where('id', $id)->update(['name' => $request->name]);
            return response()->json(['message' => 'Categories Updated'], 200);
        }catch(Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function delete_categories($id)
    { 
        $categories = Categories::find($id);
            if($categories){
                $categories->delete();
                    return response()->json(['message' => 'Categories Deleted'], 201);
            }else{
                return response()->json(['message' => 'Categories not found'], 404);
            }
    
    }

}
