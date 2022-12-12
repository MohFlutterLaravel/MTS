<?php

namespace App\Http\Controllers;

use App\Models\Payemode;
use Illuminate\Http\Request;

class PayemodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payemode = Payemode::with('user:id,name')->get();
        return response()->json($payemode, 200);
    }

    // for vue select

    public function filterData()
    {
        $payemode = Payemode::select(['id', 'mode'])->get();
        return response()->json([$payemode]);
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
            'mode' => ['required', 'max:255', 'min:4'],
            'ispay' => ['required', 'boolean'],
        ]);
        $payemode = new Payemode;
        $payemode->user_id = $request->user_id;
        $payemode->mode = $request->mode;
        $payemode->ispay = $request->ispay;
        $payemode->save();
        return response()->json([
            'succsess' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payemode  $payemode
     * @return \Illuminate\Http\Response
     */
    public function show(Payemode $payemode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payemode  $payemode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payemode $payemode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payemode  $payemode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payemode $payemode)
    {
        //
    }
}
