<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with('user:id,name')->paginate(20);
        return response()->json([
            $categories
        ]);
    }

    public function selectData()
    {
        $categories = Category::select(['id', 'designation'])->get();
        return response()->json([$categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'designation' => ['required', 'max:255', 'min:6'],
        ]);
        $category = new Category;
        $category->user_id = $request->user_id;
        $category->designation = $request->designation;
        $category->description = $request->description;
        $category->save();
        return response()->json($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'designation' => ['required', 'max:255', 'min:6'],
        ]);
        $category= Category::find($id);
        $category->user_id = $request->user_id;
        $category->designation = $request->designation;
        $category->description = $request->description;
        $category->save();
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category =Category::findOrFail($id);
        if ($category) {
            if (count($category->products) == 0) {
                $category->delete();
                return response()->json([
                    'success' => true
                ]);
            }else{
                return response()->json([
                    'success' => false,
                ],200); 
            }
        }
    }
}
