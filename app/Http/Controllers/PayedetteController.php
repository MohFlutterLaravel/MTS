<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\Client;
use App\Models\Operation;
use App\Models\Payedette;
use Illuminate\Http\Request;

class PayedetteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'client_id' => ['required', 'exists:clients,id'],
            'mantant' => ['required'],
        ]);
        $caisse = Caisse::find($request->caisse_id);
        $client = Client::find($request->client_id);
        $payedette = new Payedette;
        $payedette->user_id = $request->user_id;
        $payedette->caisse_id = $request->caisse_id;
        $payedette->client_id = $request->client_id;
        $payedette->mantant = $request->mantant;
        $payedette->observation = $request->observation;
        $payedette->save();

        $operation = new Operation;
        $operation->user_id = $request->user_id;
        $operation->caisse_id = $request->caisse_id;
        $operation->enc = $request->mantant;
        $operation->observation = 'Paye dette n°: '.$payedette->id.' || '.$client->name;
        $operation->ancien_solde = $caisse->solde;
        $operation->nv_solde = $operation->ancien_solde + $request->mantant;
        $operation->save();

        $client->dette -= $request->mantant;
        $client->save();

        $caisse->tot_enc += $request->mantant;
        $caisse->solde += $request->mantant;
        $caisse->save();

        return response()->json([
            'success' => true
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payedette  $payedette
     * @return \Illuminate\Http\Response
     */
    public function show(Payedette $payedette)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payedette  $payedette
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payedette $payedette)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payedette  $payedette
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payedette $payedette)
    {
        //
    }
}
