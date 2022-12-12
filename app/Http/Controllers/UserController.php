<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'unique:users', 'email'],
            'password' => ['required', 'min:6']
        ]);
        $data = array();
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        DB::table('users')->insert($data);
        return $this->login($request);
    }

    public function changePassword(Request $request, $id)
    {
        $validatedData = $request->validate([
            'oldpass' => ['required'],
            'password' => ['required', 'min:6'],
            'password_confirmation' => 'required|same:password',
        ]);
        $user = User::find($id);
        if(Hash::check($request->oldpass, $user->password)){
            $data = array();
            $data['password'] = Hash::make($request->password);
            DB::table('users')->update($data);
            return response()->json([
                'success' => true
            ],200);
        }
        return response()->json([
            'success' => false
        ],200);
    }
    
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        $credentials = request(['email', 'password']);
        $myTTL = 60; // 60*24*365*10 --> 10 years
        JWTAuth::factory()->setTTL($myTTL);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    
    public function me()
    {
        return response()->json(auth()->user());
    }

    
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function isValidToken()
    {
        return response()->json(['success' => true, 200]);
    }
    
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'name' => auth()->user()->name,
            'id' => auth()->user()->id,
            'success' => true
        ]);
    }

}
