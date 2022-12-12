<?php

namespace App\Http\Controllers;

use App\Models\Virement;
use App\Models\Operation;
use App\Models\Caisse;
use App\Models\Source;
use Illuminate\Http\Request;

class VirementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $virements = Virement::with(['user:id,name', 'caisse:id,label', 'source:id,name'])->paginate(20);
        return response()->json($virements, 200);
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
            'source_id' => ['required', 'exists:sources,id'],
            'mantant' => ['required'],
        ]);
        $user = auth()->user();
        $caisse = Caisse::find($request->caisse_id);
        $source = Source::find($request->source_id);

        $virement = new Virement;
        $virement->user_id = $request->user_id;
        $virement->caisse_id = $request->caisse_id;
        $virement->source_id = $request->source_id;
        $virement->mantant = $request->mantant;
        $virement->save();

        $operation = new Operation;
        $operation->user_id = $request->user_id;
        $operation->caisse_id = $request->caisse_id;
        $operation->dec += $request->mantant;
        $operation->observation = 'Virement n°: '.$virement->id.' || '.$source->name;
        $operation->ancien_solde = $caisse->solde;
        $operation->nv_solde = $operation->ancien_solde - $request->mantant;
        $operation->save();

        $caisse->tot_dec = $caisse->tot_dec + $request->mantant;
        $caisse->solde = $caisse->solde - $request->mantant;
        $caisse->save();

        $source->tot_vers = $source->tot_vers + $request->mantant;
        $source->solde = $source->solde - $request->mantant;
        $source->save();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Virement  $virement
     * @return \Illuminate\Http\Response
     */
    public function show(Virement $virement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Virement  $virement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Virement $virement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Virement  $virement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Virement $virement)
    {
        //
    }
}
