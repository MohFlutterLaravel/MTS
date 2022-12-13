<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\Client;
use App\Models\Operation;
use App\Models\Payemode;
use App\Models\Product;
use App\Models\Vente;
use Illuminate\Http\Request;

class VenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ventes = Vente::with([
            'products',
            'user:id,name',
            'caisse:id,label',
            'client:id,name',
            'payemode:id,mode'
            ])->paginate(20);
        return response()->json($ventes, 200);
    }

    public function FilterData(Request $request)
    {
        $validatedData = $request->validate([
            'dated' => ['required', 'date'],
            'datef' => ['required', 'date'],
        ]);
        $ventes = Vente::with([
            'products',
            'user:id,name',
            'caisse:id,label',
            'client:id,name',
            'payemode:id,mode'
            ])
        ->whereDate('created_at',  '>=', $request->dated)
        ->whereDate('created_at',  '<', $request->datef)
        ->get();
        $mantant = 0;
        foreach ($ventes as $vente) {
            $mantant += $vente->mantant;
        }
        return response()->json([
            'ventes' => $ventes,
            'mantant' => $mantant,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'caisse_id' => ['required', 'exists:caisses,id'],
            'payemode_id' => ['required', 'exists:payemodes,id'],
            'products' => ['required'],
        ]);

        $caisse = Caisse::find($request->caisse_id);
        $client =Client::find($request->client_id);
        $payemode = Payemode::find($request->payemode_id);
        
        $vente = new Vente;
        $vente->user_id = $request->user_id;
        $vente->client_id = $request->client_id;
        $vente->caisse_id = $request->caisse_id;
        $vente->payemode_id = $request->payemode_id;
        $vente->observation = $request->observation;
        $vente->isvalid = true;
        $vente->mantant = 0;
        $vente->save();
        
        foreach ($request->products as $product) {
            $vente->products()->attach(
                $product['id'],
                [
                    'qte' =>  $product['qte'],
                    'prix' =>$product['prix'],
                    'mantant' => $product['total'],
                ]);
                $prod = Product::find($product['id']);
                $prod->qte -= $product['qte'];
                $prod->save();
                $vente->mantant += $product['total'];
                $vente->save();

        }

        $operation = new Operation;
        $operation->user_id = $request->user_id;
        $operation->caisse_id = $caisse->id;
        $operation->enc += $vente->mantant;
        $operation->observation = 'Vente n°: '.$vente->id.' || '.$client->name.' || '.$payemode->mode;
        $operation->ancien_solde = $caisse->solde;

        if ($payemode->ispay) {
            $operation->nv_solde = $operation->ancien_solde + $vente->mantant;

            $caisse->tot_enc += $vente->mantant;
            $caisse->solde += $vente->mantant;
            $caisse->save();
        }else{
            $operation->nv_solde = $operation->ancien_solde;
            $client->dette += $vente->mantant;
        }
        $client->save();
        $operation->save();


        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vente  $vente
     * @return \Illuminate\Http\Response
     */
    public function show(Vente $vente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vente  $vente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vente $vente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vente  $vente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vente $vente)
    {
        //
    }
}
