<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiTokenController extends Controller
{

    public function register(Request $request)
    {
        //1 - form validation
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        //2 check if user exist
        $exists = User::where('email', $request->email)->exists();
        if($exists){
            return response()->json([
                'error'=>"You are already registered"
            ], 409);
        }

        // 3 create user
        $user = User::create([
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'name'=> $request->name
        ]);

        //4 create auth token
        $token = $user->createToken($request->device_name, ['tasks:write', 'tasks:read'])->plainTextToken;

        //5 return data
        return response()->json([
            'token'=> $token,
            'email'=>$user->email,
            'name'=> $user->name,
            "created_at"=> $user->created_at
        ], 201);

    }


     /**
     * @OA\Post(path="/api/auth/register",
     *   tags={"auth"},
     *   summary="Register user",
     *   description="Register a user",
     *   operationId="registerUser",
     * @OA\RequestBody(
    *    required=true,
    *    description="User email, name, password, device_name for register",
    *    @OA\JsonContent(
    *       required={"email", "name", "password"},
    *       @OA\Property(property="email", type="string", format="email"),
    *       @OA\Property(property="name", type="string"),
    *       @OA\Property(property="password", type="string", format="password"),
    *       @OA\Property(property="device_name", type="string", example="Ios"),
    *    ),
    * ),
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="token", type="string"),
    *       @OA\Property(property="name", type="string"),
    *       @OA\Property(property="email", type="string"),
    *       @OA\Property(property="created_at", type="date-time"),
    *       
    
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Please fill in all fields."),
    *        )
    *     ),
    *   @OA\Response(
    *    response=409,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="You are already registered."),
    *        )
    *     ),
     * )
     */

     /**
     * @OA\Post(path="/api/auth/login",
     *   tags={"auth"},
     *   summary="Login user",
     *   description="",
     *   operationId="loginUser",
     * @OA\RequestBody(
    *    required=true,
    *    description="User email and password for login",
    *    @OA\JsonContent(
    *       required={"email","password"},
    *       @OA\Property(property="email", type="string", format="email"),
    *       @OA\Property(property="password", type="string", format="password"),
    *       @OA\Property(property="device_name", type="string", example="Ios"),
    *    ),
    * ),
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="token", type="string"),
    *       @OA\Property(property="name", type="string"),
    *       @OA\Property(property="email", type="string")
    *       
    
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Please fill in all fields."),
    *        )
    *     ),
    *   @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="The provided credentials are incorrect."),
    *        )
    *     ),
     * )
     */

}
