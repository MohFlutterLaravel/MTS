<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\Caisse;
use App\Models\Operation;
use App\Models\Payemode;
use App\Models\Product;
use App\Models\Source;
use App\Models\Virement;
use Illuminate\Http\Request;

class AchatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * user

     */
    public function index()
    {
        $achats = Achat::with([
            'products:id,designation',
            'user:id,name',
            'caisse:id,label',
            'source:id,name',
            'payemode:id,mode'
            ])->paginate(20);
        return response()->json($achats, 200);
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
            'source_id' => ['required', 'exists:sources,id'],
            'caisse_id' => ['required', 'exists:caisses,id'],
            'payemode_id' => ['required', 'exists:payemodes,id'],
        ]);
        
        $caisse = Caisse::find($request->caisse_id);
        $source =Source::find($request->source_id);
        $payemode = Payemode::find($request->payemode_id);
        
        
        $achat = new Achat;
        $achat->user_id = $request->user_id;
        $achat->source_id = $request->source_id;
        $achat->caisse_id = $request->caisse_id;
        $achat->payemode_id = $request->payemode_id;
        $achat->observation = $request->observation;
        $achat->isvalid = true;
        $achat->mantant = 0;
        $achat->save();
        foreach ($request->products as $product) {
            $achat->products()->attach(
                $product['id'],
                [
                    'qte' =>  $product['qte'],
                    'prix' =>$product['prix'],
                    'mantant' => $product['total'],
                ]);
                $prod = Product::find($product['id']);
                $prod->qte += $product['qte'];
                $prod->save();
                $achat->mantant += $product['total'];
                $achat->save();
        }

        $source->tot_achat += $achat->mantant;

        $operation = new Operation;
        $operation->user_id = $request->user_id;
        $operation->caisse_id = $caisse->id;
        $operation->dec += $achat->mantant;
        $operation->observation = 'Achat n°: '.$achat->id.' || '.$source->name.' || '.$payemode->mode;
        $operation->ancien_solde = $caisse->solde;
        
        if ($payemode->ispay) {
            $operation->nv_solde = $operation->ancien_solde - $achat->mantant;

            $caisse->tot_dec += $achat->mantant;
            $caisse->solde -= $achat->mantant;
            $caisse->save();

            $virement = new Virement;
            $virement->user_id = $request->user_id;
            $virement->caisse_id = $caisse->id;
            $virement->source_id = $source->id;
            $virement->mantant = $achat->mantant;
            $virement->save();
            $source->tot_vers += $achat->mantant;
        }else{
            $operation->nv_solde = $operation->ancien_solde;
            $source->solde += $achat->mantant;
        }
        $source->save();
        $operation->save();


        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Achat  $achat
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $achat = Achat::find($id);
        return response()->json([
            'achat' => $achat,
            'products' => $achat->products
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Achat  $achat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Achat  $achat
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $achat =Achat::findOrFail($id);
        $user = auth()->user();
        $caisse = Caisse::find($achat->caisse_id);
        $source = Source::find($achat->source_id);
        $payemode = Payemode::find($achat->payemode_id);
        if ($achat) {
            
            foreach ($achat->products as $product) {
                $product->qte -= $product->pivot->qte;
                $product->save();
            }
            $achat->products()->detach();
            
            $operation = new Operation;
            $operation->user_id = $user->id;
            $operation->caisse_id = $achat->caisse_id;
            $operation->dec += $achat->mantant;
            $operation->observation = 'Delete achat n°: '.$achat->id.' || '.$source->name.' || '.$payemode->mode;
            $operation->ancien_solde = $caisse->solde;

            if ($payemode->ispay) {
                $operation->nv_solde = $operation->ancien_solde + $achat->mantant;
    
                $caisse->tot_dec -= $achat->mantant;
                $caisse->solde += $achat->mantant;
                $caisse->save();
                $source->tot_vers -= $achat->mantant;
                
            }else{
                $operation->nv_solde = $operation->ancien_solde;
                $source->solde -= $achat->mantant;
            }
            $source->tot_achat -= $achat->mantant;
            $source->save();
            $operation->save();
    
            $achat->delete();
            return response()->json([
                'success' => true
            ]);
        }
    }
}
