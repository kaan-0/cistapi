<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;


class AuthenticationController extends Controller
{
    /**
     * Registrar nuevo usuario.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|min:4',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'response_code' => 201,
                'status'        => 'success',
                'message'       => 'Successfully registered',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validation failed',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Registration failed',
            ], 500);
        }
    }

    /**
     * Login y token.
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'response_code' => 401,
                    'status'        => 'error',
                    'message'       => 'Unauthorized',
                ], 401);
            }

            $user = Auth::user();
            //dd(get_class($user));
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'Login successful',
                'user_info'     => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
                'token'       => $token,
                'token_type'  => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validation failed',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Login failed',
            ], 500);
        }
    }
//no la estoy usando, la funcion login2
    public function login2(Request $request)
{
    try {
        
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'response_code' => 401,
                'status'        => 'error',
                'message'       => 'Credenciales incorrectas',
            ], 401);
        }

        
        $user->tokens()->delete();

        
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'response_code' => 200,
            'status'        => 'success',
            'message'       => 'Login exitoso',
            'user_info'     => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'token'       => $token,
            'token_type'  => 'Bearer',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'response_code' => 422,
            'status'        => 'error',
            'message'       => 'Validación fallida',
            'errors'        => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        Log::error('Login Error: ' . $e->getMessage());

        return response()->json([
            'response_code' => 500,
            'status'        => 'error',
            'message'       => 'Error en el login',
        ], 500);
    }
}

    /**
     * obtener lista de usuarios.
     */
    public function userInfo()
    {
        try {
            $users = User::latest()->paginate(10);

            return response()->json([
                'response_code'  => 200,
                'status'         => 'success',
                'message'        => 'Fetched user list successfully',
                'data_user_list' => $users,
            ]);
        } catch (\Exception $e) {
            Log::error('User List Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Failed to fetch user list',
            ], 500);
        }
    }

    /**
     * Logout y borrar token.
     */
    public function logOut(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                $user->tokens()->delete();

                return response()->json([
                    'response_code' => 200,
                    'status'        => 'success',
                    'message'       => 'Successfully logged out',
                ]);
            }

            return response()->json([
                'response_code' => 401,
                'status'        => 'error',
                'message'       => 'User not authenticated',
            ], 401);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'An error occurred during logout',
            ], 500);
        }
    }
}