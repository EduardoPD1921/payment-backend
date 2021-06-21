<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Transaction;
use Exception;

class TransactionController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required',
            'amount' => 'required|numeric|gt:0'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();

            return response($error, 400);
        }

        try {
            $payerUser = $request->user();
            $request['payer_id'] = $payerUser->id;

            $transaction = new Transaction;
            $transaction->create($request->all());

            return response('transaction-created', 201);
        } catch(Exception $e) {
            $errorResponse = [
                'message' => 'Falha ao criar a transação.',
                'errorLog' => $e->getMessage()
            ];

            return response($errorResponse, 500);
        }
    }
}
