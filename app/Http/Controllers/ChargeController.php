<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\Charge;
use App\Models\Operation;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;

class ChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $charges = Charge::with([
            'user:id,name',
            'caisse:id,label',
            'typecharge:id,designation'
            ])
        ->paginate(20);
        return response()->json([$charges]);
    }

    public function filterData(Request $request)
    {
        
        $validatedData = $request->validate([
            'date' => ['required', 'date'],
        ]);
        
        $charge = Charge::where('created_at', '>=', $request->date)
        ->with([
            'user:id,name',
            'caisse:id,label',
            'typecharge:id,designation'
            ])
        ->get();
        return response()->json([
            $charge
        ]);
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
            'caisse_id' => ['required', 'exists:caisses,id'],
            'typecharge_id' => ['required', 'exists:typecharges,id'],
            'designation' => ['required'],
            'mantant' => ['required'],
        ]);
        $caisse = Caisse::find($request->caisse_id);
        $user = auth()->user();

        $charge = new Charge;
        $charge->user_id = $request->user_id;
        $charge->caisse_id = $request->caisse_id;
        $charge->typecharge_id = $request->typecharge_id;
        $charge->designation = $request->designation;
        $charge->mantant = $request->mantant;
        $charge->save();
        
        $operation = new Operation;
        $operation->user_id = $request->user_id;
        $operation->caisse_id = $request->caisse_id;
        $operation->dec += $request->mantant;
        $operation->observation = 'Charge n°: '.$charge->id.' || '.$user->name;
        $operation->ancien_solde = $caisse->solde;
        $operation->nv_solde = $operation->ancien_solde - $request->mantant;
        $operation->save();

        $caisse->tot_dec = $caisse->tot_dec + $request->mantant;
        $caisse->solde = $caisse->solde - $request->mantant;
        $caisse->save();

        return response()->json(['success'=>true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Charge  $charge
     * @return \Illuminate\Http\Response
     */
    public function show(Charge $charge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Charge  $charge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Charge  $charge
     * @return \Illuminate\Http\Response
     */
    public function destroy(Charge $charge)
    {
        $caisse = Caisse::find($charge->caisse_id);
        $user = auth()->user();
        $operation = new Operation;
        $operation->user_id = $user->id;
        $operation->caisse_id = $charge->caisse_id;
        $operation->enc += $charge->mantant;
        $operation->observation = 'Delete Charge n°: '.$charge->id.' || '.$user->name;
        $operation->ancien_solde = $caisse->solde;
        $operation->nv_solde = $operation->ancien_solde + $charge->mantant;
        $operation->save();

        $caisse->tot_enc +=  $charge->mantant;
        $caisse->solde +=  $charge->mantant;
        $caisse->save();

        $charge->delete();

        return response()->json(['success'=>true]);

    }
}
