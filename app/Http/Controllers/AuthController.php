<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();

            return response($error, 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'message' => 'wrong-email'
            ], 400);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'wrong-password'
            ], 400);
        }

        $token = $user->createToken($request->email)->plainTextToken;

        $response = [
            'token' => $token
        ];

        return response($response, 201);
    }
}
