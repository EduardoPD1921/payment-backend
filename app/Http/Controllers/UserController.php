<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class UserController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|unique:users',
            'birth_date' => 'required|date_format:d/m/Y',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();

            return response($error, 400);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->birth_date = date("Y-d-m", strtotime($request->birth_date));
        $user->phone_number = $request->phone_number;
        $user->password = Hash::make($request->password);
        $user->save();

        return response('user-created', 201);
    }

    public function getUserInfo(Request $request) {
        return $request->user();
    }

    public function updateProfileImage(Request $request) {
        $file = $request->file('image');
        $file_path = $file->getPath();

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://api.imgur.com/3/image', [
            'headers' => [
                'authorization' => 'Client-ID ' . '28589de386dc032',
                'content-type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'image' => base64_encode(file_get_contents($request->file('image')->path($file_path)))
            ]
        ]);

        $imageLink = data_get(response()->json(json_decode(($response->getBody()->getContents())))->getData(), 'data.link');

        $user = $request->user();

        $user->image = $imageLink;
        $user->save();
    }

    public function update(Request $request) {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'phone_number' => 'required|string',
            'birth_date' => 'required|date_format:d/m/Y'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();

            return response($error, 400);
        }

        $user = $request->user();

        if ($user->phone_number !== $request->phone_number) {
            $checkPhoneNumber = User::where('phone_number', $request->phone_number)->first();

            if ($checkPhoneNumber) {
                return response([
                    'message' => 'phone-number-has-already-been-taken'
                ], 400);
            }

            $user->phone_number = $request->phone_number;
        }

        $user->name = $request->name;
        $user->birth_date = date("Y-d-m", strtotime($request->birth_date));
        $user->save();

        return response([
            'message' => 'changes-updated'
        ], 200);
    }
}
