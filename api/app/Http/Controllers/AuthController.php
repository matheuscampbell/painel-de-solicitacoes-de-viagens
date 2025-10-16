<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use JWTAuth;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);//login, register methods won't go through the api guard
    }

    /**
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $msg = "Validation failed.";
            foreach ($validator->errors()->all() as $error) {
                $msg = $msg . " " . $error;
            }

            return response()->json($msg, 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!auth()->user()->is_active) {
            auth()->logout();
            return response()->json(['error' => 'Conta desativada.'], 403);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            $msg = "Validation failed, please check:";
            foreach ($validator->errors()->all() as $error) {
                $msg = $msg . " " . $error;
            }
            return response()->json($msg, 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password'))
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function getaccount()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() //mention the guard name inside the auth fn
        ]);
    }

    public function checkToken(): JsonResponse
    {
        if (auth()->check()) {
            return response()->json(['message' => 'Token is valid'], 200);
        } else {
            return response()->json(['message' => 'Token is invalid'], 401);
        }
    }
}
