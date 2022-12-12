<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with(['category:id,designation', 'type:id,type_name'])->paginate(20);
        return response()->json([
            $products
        ]);
    }

    // for vue select

    public function filterData()
    {
        $sources = Product::select(['id', 'barecode','designation', 'pa', 'pv', 'tva'])->get();
        return response()->json([$sources]);
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
            'category_id' => ['required', 'exists:categories,id'],
            'type_id' => ['required', 'exists:types,id'],
            'designation' => ['required', 'max:50', 'min:4'],
            'barecode' => ['required'],
            'qte' => ['required'],
            'pa' => ['required'],
            'pv' => ['required'],
            
        ]);
        $product = new Product;
        $product->user_id = $request->user_id;
        $product->category_id = $request->category_id;
        $product->type_id = $request->type_id;
        $product->designation = $request->designation;
        $product->barecode = $request->barecode;
        $product->qte = $request->qte;
        $product->pa = $request->pa;
        $product->pv = $request->pv;
        $product->tva = $request->tva;
        $product->save();
        return response()->json([
            'success' => true
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'type_id' => ['required', 'exists:types,id'],
            'designation' => ['required', 'max:50', 'min:4'],
            'barecode' => ['required'],
            'qte' => ['required'],
            'pa' => ['required'],
            'pv' => ['required'],
            
        ]);
        $product = Product::find($id);
        $product->user_id = $request->user_id;
        $product->category_id = $request->category_id;
        $product->type_id = $request->type_id;
        $product->designation = $request->designation;
        $product->barecode = $request->barecode;
        $product->qte = $request->qte;
        $product->pa = $request->pa;
        $product->pv = $request->pv;
        $product->tva = $request->tva;
        $product->save();
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product =Product::findOrFail($id);
        if ($product->qte == 0) {
            $product->delete();
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
    
}
