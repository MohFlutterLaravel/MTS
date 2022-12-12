<?php

namespace App\Http\Controllers;

use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sources = Source::with('user:id,name')->paginate(10);
        return response()->json([$sources]);
    }
    // for vue select

    public function filterData()
    {
        $sources = Source::select(['id', 'name'])->get();
        return response()->json([$sources]);
    }



    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'max:255', 'min:2'],
        ]);
        $source = new Source;
        $source->user_id = $request->user_id;
        $source->name = $request->name;
        $source->phone = $request->phone;
        $source->address = $request->address;
        $source->save();
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Http\Response
     */
    public function show(Source $source)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);
        $source = Source::find($id);
        $source->user_id = $request->user_id;
        $source->name = $request->name;
        $source->phone = $request->phone;
        $source->address = $request->address;
        $source->save();
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $source = Source::find($id);
        if ($source->solde == 0) {
            $source->delete();
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
}
