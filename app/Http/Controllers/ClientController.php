<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function index()
    {
        $clients = Client::paginate(10);
        return response()->json([
          $clients  
        ]);
    }
    // for vue select

    public function filterData()
    {
        $clients = Client::select(['id', 'name'])->get();
        return response()->json([$clients]);
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
            'name' => ['required', 'max:255', 'min:2'],
            'phone' => ['required'],
        ]);
        $client = new Client;
        $client->user_id = $request->user_id;
        $client->name = $request->name;
        $client->phone = $request->phone;
        $client->dette = 0.0;
        $client->save();

        return response()->json([
            'success' =>true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required'],
        ]);
        $client = Client::find($id);
        $client->user_id = $request->user_id;
        $client->name = $request->name;
        $client->phone = $request->phone;
        $client->save();
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = Client::find($id);
        if ($client->dette == 0) {
            $client->delete();
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
}
