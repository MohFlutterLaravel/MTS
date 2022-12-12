<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $operations = Operation::with(['user:id,name', 'caisse:id,label,solde_ini'])->paginate(10);
        return response()->json([$operations]);
    }

    public function filterData()
    {
        $caisses = Caisse::select('id', 'label')->get();
        $users = User::select('id', 'name')->get();
        return response()->json([
            'caisses' => $caisses,
            'users' => $users
        ]);
    }

    public function resultFilter(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'caisse_id' => ['required', 'exists:caisses,id'],
            'dated' => ['required', 'date'],
            'datef' => ['required', 'date'],
        ]);
        $operations = Operation::with(['user:id,name', 'caisse:id,label,solde_ini'])
        ->where('user_id', '=', $request->user_id)
        ->where('caisse_id', '=', $request->caisse_id)
        ->whereDate('created_at',  '>', $request->dated)
        ->whereDate('created_at',  '<', $request->datef)
        ->get();
        return response()->json([
            $operations
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Operation  $operation
     * @return \Illuminate\Http\Response
     */
    public function show(Operation $operation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Operation  $operation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Operation $operation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Operation  $operation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Operation $operation)
    {
        //
    }
}
