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
use App\Http\Requests\UpdateCardRequest;
use App\Http\Requests\ValidatePasswordRequest;
use Illuminate\Support\Facades\Auth as Auth_Sanctum;





class UserController extends Controller
{
    

    public function index(Request $request)
    {
        $query = User::query();

        
        
        if ($request->has('type')) {
            $userType = $request->input('type');
            $query->where('type', $userType);
        }

        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', $search . '%');
        }

        $users = $query->paginate(10);

        return UserResource::collection($users);
    }

    public function showMe(Request $request)
    {
        return new UserResource($request->user());
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    
        public function store(StoreUserRequest $request)
    {

        $user = new User();
        $validatedData = $request->validated();

        // Manually assign the validated fields to the user model, excluding the photo_filename field
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->nickname = $validatedData['nickname'];
        $user->blocked = false;
        if($validatedData['type'] == 'A'){
            $user->brain_coins_balance = 0;
        }else{
            $user->brain_coins_balance = 10;
        }
        $user->password = Hash::make($request->password); // Hash the password
        
        
        if($request->user()?->type == 'A'){
            $user->type = 'A';
        }else{
            $user->type = 'P';
        }



        if ($user->save()) {
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->brain_coins = 10;
            $transaction->type = 'B';
            $transaction->transaction_datetime = now();
            $transaction->save();

            // Check if the photo is being uploaded as a Base64 string
            if ($request->photo_filename) {
                // Decode the Base64 string
                $image = $request->photo_filename;  // your base64 encoded
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageData = base64_decode($image);

                
                $extension = 'png'; // Default to png, change if necessary
                if (strpos($request->photo_filename, 'data:image/jpeg;base64,') === 0) {
                    $extension = 'jpg';
                }
                $filename = $user->id . '_'. uniqid().'.' . $extension;

                // Store the image in the public disk
                Storage::disk('public')->put('photos/' . $filename, $imageData);

                // Update the user's photo_filename field
                $user->photo_filename = $filename;
                $user->save(); // Save the user to the database
            }

            return new UserResource($user);
        }

        return response()->json(['error' => 'Unable to create user'], 500);
    }
    

    public function update(UpdateUserRequest $request, User $user)
    {
        

        $user_old_photo = $user->photo_filename;
        
        $user->fill($request->validated());


        // Check if the password is being updated
        if ($request->has('password')) {
            $user->password = Hash::make($request->password); // Hash the password
        }

        //dd( $request->file('photo_filename'));

        // Check if the photo is being updated
        if ($request->photo_filename) {
            
            // Delete the existing photo if it exists
            if (Storage::disk('public')->exists('photos/' . $user_old_photo)) {
                Storage::disk('public')->delete('photos/' . $user_old_photo);
            }

            
            // Decode the Base64 string
            $image = $request->photo_filename;  // your base64 encoded
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            
            $extension = 'png'; // Default to png, change if necessary
            if (strpos($request->photo_filename, 'data:image/jpeg;base64,') === 0) {
                $extension = 'jpg';
            }
            $filename = $user->id . '_'. uniqid().'.' . $extension;

            // Store the image in the public disk
            Storage::disk('public')->put('photos/' . $filename, $imageData);

            // Update the user's photo_filename field
            $user->photo_filename = $filename;
            
        }

        $user->save(); // Save the user to the database
        $user->updated_at = now();

        return new UserResource($user);
    }

    public function updateCards(UpdateCardRequest $request, User $user){
        $auth_user = Auth_Sanctum::user();
        if($auth_user->id != $user->id || $auth_user->type == 'A'){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user->custom = $request->data;
        $user->save();
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
        $user->brain_coins_balance = 0;
        $user->save();

        

        return response()->json([
            'message' => 'ID: '. $user->id .', Name: '. $user->name . ' deleted!'
        ]);
    }

    
        
    
}
