<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticateController extends Controller
{


    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            $token = $user->createToken('access_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'error' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'An error occurred while creating the user.',
            ], 500);
        }

    }

    public function login(Request $request)
    {
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->first();
        $token = $user->createToken('access_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logged out successfully']);
    }
}
