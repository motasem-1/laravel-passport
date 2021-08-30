<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PassportController extends Controller
{
    // register by api
    public function register(Request $request)
    {
        /// check validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        /// if validation failed
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        /// if validation success create new user
        $users = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),


        ]);
        /// generate new token
        $token = $users->createToken('ZyoodAcademe')->accessToken;

        return response()->json(['token' => $token], 200);
    } // end of register function


    // login user by api
    public function login(Request $request)
    {

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        /// check if user is exists in database
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('ZyoodAcademe')->accessToken;

            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'unSAuthorized', 401]);
        }
    }


        public function logout(Request $request)
        {
            auth()->user()->token()->revoke();

            return response()->json([
            'success'=>true,
            'data' =>'logout successfully'
                ],200);
            
        }

    public function details()
    {

        return response()->json(['user' => auth()->user()], 200);
    }
}