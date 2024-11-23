<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function showMe($id)
    {
        return new UserResource(User::find($id));
    }

    public function store(StoreUserRequest $request){
        $user = new User();
        $user->fill($request->validated());
        $user->blocked = false;
        $user->brain_coins_balance = 10;
        $user->email_verified_at = null;
        $user->password = Hash::make($request->password); // Hash the password
        $user->created_at = now();
        $user->updated_at = null;
        $user->type = 'p';

        //Check if the photo is being uploaded
        if($request->hasFile('photo_filename')){
            
            $path = $request->photo_file->store('public/photos');
            $request->user()->photo_filename = basename($path);
            $request->user()->save();
        }

        $user->save(); // Save the user to the database

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, $id){
        $user = User::find($id);
        $user->fill($request->validated());

        //Check if the password is being updated
        if($request->has('password')){
            $user->password = Hash::make($request->password); // Hash the password
        }

        //Check if the photo is being updated
        if($request->hasFile('photo_filename')){
            // Delete previous file (if any)
            if (
                $request->user()->photo_filename &&
                Storage::fileExists('public/photos/' . $request->user()->photo_filename)
            ) {
                Storage::delete('public/photos/' . $request->user()->photo_filename);
            }
            $path = $request->photo_file->store('public/photos');
            $request->user()->photo_filename = basename($path);
            $request->user()->save();
        }

        $user->updated_at = now();

        $user->save();

        return new UserResource($user);
    }

    public function block($id){
        $user = User::find($id);
        $user->blocked = !$user->blocked;
        $user->save();

        return new UserResource($user);
    }

    public function destroy($id){
        $user = User::find($id);
        $user->delete();

        return response()->json([
            'message' => 'ID: '. $user->id .', Name: '. $user->name . ' deleted!'
        ]);
    }
}
