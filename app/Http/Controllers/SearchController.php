<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class SearchController extends Controller
{
    public function search(Request $request) {
        $search = $request->search;

        if ($search) {
            $result = User::query()
                ->where('name', 'LIKE', '%'.$search.'%')
                ->orWhere('email', 'LIKE', '%'.$search.'%')
                ->get();

            if ($result->isEmpty()) {
                return response([
                    'message' => 'user-not-found'
                ], 404);
            }

            return response($result, 200);
        }

        return [];
    }

    public function returnUserSearched(Request $request) {
        $id = $request->id;

        $user = User::findOrFail($id);

        return $user;
    }
}
