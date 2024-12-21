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
use Illuminate\Support\Facades\Auth as Auth_Sanctum;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\DB;


class TransactionController extends Controller
{
    public function index()
    {
        return TransactionResource::collection(Transaction::orderBy('transaction_datetime', 'desc')->paginate(5));

    }


    public function store(StoreTransactionRequest $request)
    {
        $user = Auth_Sanctum::user();
        DB::beginTransaction();

        if ($user->brain_coins_balance + $request->brain_coins < 0){
            DB::rollBack();
            return response()->json(['error' => 'Transaction failed: You do not have enough funds'], 500);
        } 

        if($user->type == 'A'){
            DB::rollBack();
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        try {
            $transaction = new Transaction();
            $transaction->fill($request->validated());
            $transaction->transaction_datetime = now();
            $transaction->save();
            if ($request->type === 'P' || ($request->type == 'I' && $request->game_id == null)) {
                $user = $transaction->user;
                $user->brain_coins_balance += $transaction->brain_coins;
                $user->save();
            }
    
            DB::commit();
            return new TransactionResource($transaction);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
    }
    
    public function showUserTransactions( string $nickname)
    {
        $user = Auth_Sanctum::user();

        if($user->nickname != $nickname && $user->type != 'A'){
            return response()->json(['message' => "The Logged Account Don't Have Permission to Access This User Transactions "], 403);
        }

        $user = User::where('nickname', $nickname)->first();



        if($user == null){
            return response()->json(['message' => 'Invalid nickname'], 404);
        }

        return TransactionResource::collection($user->transactions()->orderBy('transaction_datetime', 'desc')->paginate(5));
    }
    
    public function showTransactionsByType(string $type)
    {
        if($type != 'I' && $type != 'B' && $type != 'P'){
            return response()->json(['message' => 'Invalid type'], 404);
        }
        
        if($type == 'I'){
            $transactions = Transaction::where('type', 'I')->orderBy('transaction_datetime','desc')->paginate(5);
            return InternalTransactionResource::collection($transactions);
        }
        elseif($type == 'B'){
            $transactions = Transaction::where('type', 'B')->orderBy('transaction_datetime','desc')->paginate(5);
            return BonusResource::collection($transactions);
        }
        else{
            $transactions = Transaction::where('type', 'P')->orderBy('transaction_datetime','desc')->paginate(5);
            return PurchaseResource::collection($transactions);
        }
    }

    public function showTransactionsByTypeAndUser(string $nickname, string $type)
    {
        $user = Auth_Sanctum::user();
        
        if($user->nickname != $nickname && $user->type != 'A'){
            return response()->json(['message' => "The Logged Account Doesn't Have Permission to Access This User Transactions "], 403);
        }

        $user = User::where('nickname', $nickname)->first();
        if($user == null){
            return response()->json(['message' => 'Invalid nickname'], 404);
        }

        if($type != 'I' && $type != 'B' && $type != 'P'){
            return response()->json(['message' => 'Invalid type'], 404);
        }
        
        if($type == 'I'){
            $transactions = Transaction::where('type', 'I')->where('user_id', $user->id)->orderBy('transaction_datetime','desc')->paginate(5);
            return InternalTransactionResource::collection($transactions);
        }
        elseif($type == 'B'){
            $transactions = Transaction::where('type', 'B')->where('user_id', $user->id)->orderBy('transaction_datetime','desc')->paginate(5);
            return BonusResource::collection($transactions);
        }
        else{
            $transactions = Transaction::where('type', 'P')->where('user_id', $user->id)->orderBy('transaction_datetime','desc')->paginate(5);
            return PurchaseResource::collection($transactions);
        }
    }


}
