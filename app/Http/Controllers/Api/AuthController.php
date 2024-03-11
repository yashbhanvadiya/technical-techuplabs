<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Hash;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try{ 
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
            ]);

            if($validator->fails()) {
                return $this->sendError($validator->errors()->first(), [], 422);
            }

            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('login')->accessToken;

                return $this->sendResponse(['token' => $token], 'User login successfully');
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function register(Request $request)
    {
        try{ 
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|max:20',
                'email' => 'required|string|email|max:50|unique:users',
                'password' => 'required|string|min:8',
            ]);
            
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), [], 422);
            }

            $user = $this->user::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return $this->sendResponse($user, 'user added successfully');

        } catch (\Exception $e) {
            return $this->sendError('something went wrong', 500);
        }
    }
}
