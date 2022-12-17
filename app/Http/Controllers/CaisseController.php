<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\Operation;
use Illuminate\Http\Request;

class CaisseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $caisses = Caisse::with('user:id,name')->paginate(5);
        return response()->json([
            $caisses
        ]);
    }

    public function filterData()
    {
        $caisses = Caisse::select(['id', 'label'])->get();
        return response()->json([$caisses]);
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
            'label' => ['required', 'max:255', 'min:6'],
            'solde_ini' => ['required'],
        ]);
        $caisse = new Caisse;
        $caisse->user_id = $request->user_id;
        $caisse->label = $request->label;
        $caisse->solde_ini = $request->solde_ini;
        $caisse->solde = $request->solde_ini;
        $caisse->save();
        return response()->json([
            'success' => true
        ]);
    }

    public function encaisser(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'caisse_id' => ['required', 'exists:caisses,id'],
            'encaissement' => ['required'],
        ]);
        $caisse = Caisse::find($request->caisse_id);
        $user = auth()->user();

        $operation = new Operation;
        $operation->user_id = $request->user_id;
        $operation->caisse_id = $caisse->id;
        $operation->enc += $request->encaissement;
        $operation->observation = 'Encaissement par  '.$user->name;
        $operation->ancien_solde = $caisse->solde;
        $operation->nv_solde = $operation->ancien_solde + $request->mantant;
        $operation->save();

        $caisse->tot_enc = $caisse->tot_enc + $request->encaissement;
        $caisse->solde = $caisse->solde + $request->encaissement;
        $caisse->save();

        return response()->json(['success'=>true]);
    }

    public function decaisser(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'caisse_id' => ['required', 'exists:caisses,id'],
            'decaissement' => ['required'],
        ]);
        $caisse = Caisse::find($request->caisse_id);
        $user = auth()->user();

        $operation = new Operation;
        $operation->user_id = $request->user_id;
        $operation->caisse_id = $caisse->id;
        $operation->dec += $request->decaissement;
        $operation->observation = 'Decaissement par  '.$user->name;
        $operation->ancien_solde = $caisse->solde;
        $operation->nv_solde = $operation->ancien_solde - $request->decaissement;
        $operation->save();

        $caisse->tot_dec = $caisse->tot_dec + $request->decaissement;
        $caisse->solde = $caisse->solde - $request->decaissement;
        $caisse->save();

        return response()->json(['success'=>true]);
    }


    public function CaissesChart()
    {
        $caisses = Caisse::select(['id', 'label', 'tot_enc', 'tot_dec', 'solde'])->get();
        return response()->json([$caisses]);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Caisse  $caisse
     * @return \Illuminate\Http\Response
     */
    public function show(Caisse $caisse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Caisse  $caisse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Caisse $caisse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Caisse  $caisse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Caisse $caisse)
    {
        //
    }
}
