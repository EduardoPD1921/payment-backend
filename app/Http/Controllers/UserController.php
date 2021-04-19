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
            'name' => 'required|string|min:5|max:30',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|min:11|unique:users',
            'birth_date' => 'required|date_format:d/m/Y',
            'password' => 'required|string|min:10'
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

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:5|max:30',
            'phone_number' => 'required|string|min:11',
            'birth_date' => 'required|date_format:d/m/Y',
            'image' => 'sometimes|nullable'
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

        if ($request->image) {
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

            $user->image = $imageLink;
        }

        $user->name = $request->name;
        $user->birth_date = date("Y-d-m", strtotime($request->birth_date));
        $user->save();

        return response([
            'message' => 'changes-updated'
        ], 200);
    }

    public function emailUpdate(Request $request) {
        $validator = Validator::make($request->all(), [
            'oldEmail' => 'required|email',
            'newEmail' => 'required|email'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();

            return response($error, 400);
        }

        $checkUniqueEmail = User::where('email', $request->newEmail)->first();

        if ($checkUniqueEmail) {
            return response([
                'message' => 'email-already-in-use'
            ], 400);
        }

        $user = $request->user();

        if ($request->oldEmail === $user->email) {
            $user->email = $request->newEmail;
            $user->save();

            return response([
                'message' => 'email-updated'
            ], 200);
        }

        return response([
            'message' => 'wrong-old-email'
        ], 400);
    }

    public function passwordUpdate(Request $request) {
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();

            return response($error, 400);
        }

        $user = $request->user();

        if (Hash::check($request->oldPassword, $user->password)) {
            $user->password = Hash::make($request->newPassword);
            $user->save();

            return response([
                'message' => 'password-updated'
            ], 200);
        }

        return response([
            'message' => 'wrong-old-password'
        ], 400);
    }
}
