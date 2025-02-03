<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        // validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => "All fields are mandatory",
                'error' => $validator->messages()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message'=> 'The provided credentials are incorrect'
            ], 401);
        }

        $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successful',
            'token_type' => 'Bearer',
            'token' => $token
        ], 200);
    }

    public function register(Request $request): JsonResponse {

        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => "All fields are mandatory",
                'error' => $validator->messages()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if($user) {
            $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;

            return response()->json([
                'message' => 'Registration Successful',
                'token_type' => 'Bearer',
                'token' => $token
            ], 201);
        }
        else {
            return response()->json([
                'message' => 'Registration Unsuccessful'
            ], 500);
        }
    }

    public function profile(Request $request) {
        if ($request->user()) {
            return response()->json([
                'message' => 'Profile fetched',
                'date' => $request->user()
            ], 200);
        }
        else {
            return response()->json([
                'message' => 'Not Authenticated'
            ], 401);
        }
    }

    public function logout(Request $request) {
        $user = User::where('id', $request->user()->id)->first();

        if($user){
            // get all tokens and delete them
            $user->tokens()->delete();
            // send a message
            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        }
        else {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }
}
