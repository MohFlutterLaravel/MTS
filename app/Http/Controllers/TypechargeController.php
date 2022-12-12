<?php

namespace App\Http\Controllers;

use App\Models\Typecharge;
use Illuminate\Http\Request;

class TypechargeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typec = Typecharge::all();
        return response()->json([$typec]);
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
            'designation' => ['required'],
        ]);
        $typec = new Typecharge;
        $typec->user_id = $request->user_id;
        $typec->designation = $request->designation;
        $typec->save();
        return response()->json($typec);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Typecharge  $typecharge
     * @return \Illuminate\Http\Response
     */
    public function show(Typecharge $typecharge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Typecharge  $typecharge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Typecharge $typecharge)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Typecharge  $typecharge
     * @return \Illuminate\Http\Response
     */
    public function destroy(Typecharge $typecharge)
    {
        //
    }
}
