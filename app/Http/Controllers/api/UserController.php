<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Transaction;
use App\Http\Controllers\api\TransactionController;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function showMe(Request $request)
    {
        return new UserResource($request->user());
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function store(StoreUserRequest $request){
        $user = new User();
        $user->fill($request->validated());
        $user->blocked = false;
        $user->brain_coins_balance = 10;
        $user->password = Hash::make($request->password); // Hash the password
        $user->type = 'p';


        //Check if the photo is being uploaded
        if ($request->hasFile('photo_filename')) {
            
    
                // Store the new photo temporarily
            $path = $request->file('photo_filename')->store('photos', 'public');
            $filename = $user->id . '_' . basename($path);

            // Move the file to the new name
            Storage::move('public/photos/' . basename($path), 'public/photos/' . $filename);

            // Update the user's photo_filename attribute
            $user->photo_filename = $filename;
        }

        if( $user->save()){
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->brain_coins = 10;
            $transaction->type = 'B';
            $transaction->transaction_datetime = now();
            $transaction->save();
            
            return new UserResource($user);
        }

       

        
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        //$user = User::findOrFail($id);
        $user->fill($request->validated());


        // Check if the password is being updated
        if ($request->has('password')) {
            $user->password = Hash::make($request->password); // Hash the password
        }

        //dd( $request->file('photo_filename'));

        // Check if the photo is being updated
        if ($request->hasFile('photo_filename')) {
            // Delete the existing photo if it exists
            if ($user->photo_filename && Storage::exists('public/photos/' . $user->photo_filename)) {
                Storage::delete('public/photos/' . $user->photo_filename);
            }

            
    
                // Store the new photo temporarily
            $path = $request->file('photo_filename')->store('photos', 'public');
            $filename = $user->id . '_' . basename($path);

            // Move the file to the new name
            Storage::move('public/photos/' . basename($path), 'public/photos/' . $filename);

            // Update the user's photo_filename attribute
            $user->photo_filename = $filename;
        }

        $user->save(); // Save the user to the database
        $user->updated_at = now();

        return new UserResource($user);
    }

    public function block(User $user){
        //$user = User::find($id);
        $user->blocked = !$user->blocked;
        $user->save();

        return new UserResource($user);
    }

    public function destroy(User $user){
        //$user = User::find($id);
        $user->delete();

        return response()->json([
            'message' => 'ID: '. $user->id .', Name: '. $user->name . ' deleted!'
        ]);
    }
}
