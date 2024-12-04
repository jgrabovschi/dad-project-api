<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\InternalTransactionResource;
use App\Http\Resources\BonusResource;
use App\Http\Resources\PurchaseResource;
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

    public function showUserTransactions(string $nickname)
    {
        $user = User::where('nickname', $nickname)->first();
        return TransactionResource::collection($user->transactions);
    }
    
    public function showTransactionsByType(string $type)
    {
        if($type == 'I'){
            $transactions = Transaction::where('type', 'I')->get();
            return InternalTransactionResource::collection($transactions);
        }
        elseif($type == 'B'){
            $transactions = Transaction::where('type', 'B')->get();
            return BonusResource::collection($transactions);
        }
        else{
            $transactions = Transaction::where('type', 'P')->get();
            return PurchaseResource::collection($transactions);
        }
    }

    public function showTransactionsByTypeAndUser(string $nickname, string $type)
    {
        $user = User::where('nickname', $nickname)->first();
        if($type == 'I'){
            $transactions = Transaction::where('type', 'I')->where('user_id', $user->id)->get();
            return InternalTransactionResource::collection($transactions);
        }
        elseif($type == 'B'){
            $transactions = Transaction::where('type', 'B')->where('user_id', $user->id)->get();
            return BonusResource::collection($transactions);
        }
        else{
            $transactions = Transaction::where('type', 'P')->where('user_id', $user->id)->get();
            return PurchaseResource::collection($transactions);
        }
    }


}
