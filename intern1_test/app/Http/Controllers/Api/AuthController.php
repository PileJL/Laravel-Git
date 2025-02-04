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
        request()->validate(
            [
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:8|max:255'
                ]
        );

        // get user with email from request
        $user = User::where('email', $request->email)->first();

        // check if user exists or the Haash request->password equals to email's password
        if(!$user || !Hash::check($request->password, $user->password)) {
            // return error message if incorrect credentials
            return response()->json([
                'message'=> 'The provided credentials are incorrect'
            ], 401);
        }

        // create user token
        $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;

        // return confirmation message, token_type, and token
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

        // if validator is triggered, return error message
        if($validator->fails()) {
            return response()->json([
                'message' => "All fields are mandatory",
                'error' => $validator->messages()
            ], 422);
        }

        // add user to DB
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // if successfully added user to DB
        if($user) {
            // create token
            $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;
            // return confirmation message along with token type and token itself
            return response()->json([
                'message' => 'Registration Successful',
                'token_type' => 'Bearer',
                'token' => $token
            ], 201);
        }
        else {
            // if unsuccessful user addition, return error message
            return response()->json([
                'message' => 'Registration Unsuccessful'
            ], 500);
        }
    }

    public function profile(Request $request) {
        // get user with token from request
        $user = $request->user();
        // check if user exist based on request's token
        if ($user) {
            // return confirmation message along with user's profile
            return response()->json([
                'message' => 'Profile fetched',
                'date' => $user
            ], 200);
        }
        else {
            // return error message if user doesn't exist
            return response()->json([
                'message' => 'Not Authenticated'
            ], 401);
        }
    }

    public function logout(Request $request) {

        // get user with ID from request's token
        $user = User::where('id', $request->user()->id)->first();
        // check if user is not empty
        if($user){
            // get all user's tokens and delete them all
            $user->tokens()->delete();
            // return logout confirmation message
            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        }
        else {
            // return error message if user is not found
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }
}
