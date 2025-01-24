<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {

         $val = $request->validate([
            "name"=>"required|string|max:25",
            "email"=>"required|string|email|max:225|unique:users,email",
            "password"=>"required|string|min:3"
        ]);
        //  $val = $request->validate([
        //     "name"=>"required|string|max:25",
        //     "email"=>"required|string|email|max:225|unique::user",
        //     "password"=>"required|string|min:8"
        // ]);

        $user = User::create([
            "name"=>$request->name,
            "email"=>$request->email,
            "password"=> Hash::make($request->password)
        ]);

       $token = $user->createToken('auth_token')->plainTextToken;

       return response()->json(["access_token"=>$token, "token_type"=>"Bearer"]);

    }

    public function login(Request $request)
    {
        $request->validate([
            "email"=>"email|required|string",
           "password"=>"required|string",
        ]);

        $user = User::where("email", $request->email)->first();

        if(!$user || !hash::check($request->password, $user->password))
        {
            // throw ValidationException::withMessages([
            //     'email' => ['The provided credentials are incorrect.'],
            // ]);
            return failedApiResponse("Incorrect Email or password");
    }

    $user->tokens()->delete();
    $token = $user->createToken('auth_token')->plainTextToken;
    // $user["token"]=$token;

    return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    // return successApiResponse("successfully login", $user);
}

public function dash(User $user)
{
    return response()->json($user);
}

}
