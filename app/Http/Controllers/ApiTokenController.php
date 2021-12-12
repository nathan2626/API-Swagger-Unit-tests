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

    public function login(Request $request)
    {
        //1 - form validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                "error"=> "The provided credentials are incorrect."
            ], 401);
//            throw ValidationException::withMessages([
//                'email' => ['The provided credentials are incorrect.'],
//            ]);
        }

        //3 - clear old tokens
        $user->tokens()->where('tokenable_id', $user->id)->delete();

        //4 - create other token
        $token = $user->createToken($request->device_name, ['tasks:write', 'tasks:read'])->plainTextToken;

        return response()->json([
            'token'=> $token,
            'email'=>$user->email,
            'name'=> $user->name,
            'created_at'=> $user->created_at,
        ], 200);


    }

    public function me (Request $request)
    {
//        $user = User::where('email', $request->email)->first();
//        $current_token = $request->user()->currentAccessToken()->get();
//        $user_token = $user->tokens()->where('tokenable_id', $user->id)->get();
//        if(!$current_token === $user_token){
//            return response()->json([
//                "error"=> "The provided credentials are incorrect."
//            ], 401);
//        }

        return response()->json([
            'email'=>$request->user()->email,
            'name'=> $request->user()->name,
            "created_at"=> $request->user()->created_at,
            "updated_at"=> $request->user()->updated_at,
            "id"=> $request->user()->id

        ], 200);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message'=>"You are disconnected"
        ], 200);
    }


     /**
     * @OA\Post(path="/api/auth/register",
     *   tags={"auth"},
     *   summary="Register user",
     *   description="",
     *   operationId="registerUser",
     * @OA\RequestBody(
    *    required=true,
    *    description="User email, name, password, device_name for register",
    *    @OA\JsonContent(
    *       required={"email", "name", "password"},
    *       @OA\Property(property="email", type="email", format="email"),
    *       @OA\Property(property="name", type="string"),
    *       @OA\Property(property="password", type="password", format="password"),
    *       @OA\Property(property="device_name", type="string", example="Ios"),
    *    ),
    * ),
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="token", type="string"),
    *       @OA\Property(property="name", type="string"),
    *       @OA\Property(property="email", type="email"),
    *       @OA\Property(property="created_at", type="date-time"),
    *       
    
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Please fill in all fields."),
    *        )
    *     ),
    *   @OA\Response(
    *    response=409,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="You are already registered."),
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
    *       @OA\Property(property="email", type="email", format="email"),
    *       @OA\Property(property="password", type="password", format="password"),
    *       @OA\Property(property="device_name", type="string", example="Ios"),
    *    ),
    * ),
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="token", type="string"),
    *       @OA\Property(property="name", type="string"),
    *       @OA\Property(property="email", type="email")
    *       
    
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Please fill in all fields."),
    *        )
    *     ),
    *   @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="The provided credentials are incorrect."),
    *        )
    *     ),
     * )
     */


          /**
     * @OA\Post(path="/api/auth/me",
     *   tags={"auth"},
     *   summary="Me user",
     *   description="Me user",
     *   operationId="meUser",
     * @OA\RequestBody(
    *    required=true,
    *    description="",
    *    @OA\JsonContent(
    *      
    *    ),
    * ),
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="token", type="string"),
    *       @OA\Property(property="name", type="string"),
    *       @OA\Property(property="email", type="email"),
    *       @OA\Property(property="created_at", type="date-time"),
    *       
    
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Unauthorized."),
    *        )
    *     )
     * )
     */

           /**
     * @OA\Post(path="/api/auth/logout",
     *   tags={"auth"},
     *   summary="Logout user",
     *   description="Logout user",
     *   operationId="logoutUser",
     * @OA\RequestBody(
    *    required=true,
    *    description="",
    *    @OA\JsonContent(
    *      
    *    ),
    * ),
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="You are disconnected."),
    *       
    
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Unauthorized."),
    *        )
    *     )
     * )
     */

}
