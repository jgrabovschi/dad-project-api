<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\User;

class TransactionController extends Controller
{
    public function index()
    {
        return TransactionResource::collection(Transaction::all());
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = new Transaction();
        $transaction->fill($request->validated());
        $transaction->transaction_datetime = now();
        $transaction->save();

        return new TransactionResource($transaction);
    }

    public function showUserTransactions(Transaction $transaction, User $user)
    {
        return TransactionResource::collection($user->transactions);
    }
    
}
