<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Mail\RegisterMail;
use App\Service\AuthService;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\LoginResource;
use App\Http\Resources\StoreUserResource;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Authentication"},
     *     summary="Staff Login",
     *     description="This allow the staff to login to there account.",
     *     operationId="loginStaff",    
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *          type="object",
     *          required={"email","password"},
     *          @OA\Property(property="email", description="Enter staff email", type="text", example="admin@revenuehub.com"),
     *          @OA\Property(property="password", description="Enter staff password", type="password", example="12345678"),
     *          ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Login Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Register Successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=9),
     *                 @OA\Property(property="email", type="string", example="abc@example2.com"),
     *                 @OA\Property(property="phone", type="string", example="65728338352"),
     *                 @OA\Property(property="zone", type="string", example="nigeria"),
     *                 @OA\Property(property="role", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="Admin"),
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-01T20:44:43.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-01T20:44:43.000000Z"),
     *             ),
     *             @OA\Property(property="token", type="string", example="1|57653vtZoT9EW2iRBHShQyALGaeZ3PtrtPhUN6Arlpgc4fe5fe8"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="All Fields are Required",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="ensure that all required filed are properly filled"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="email", type="string", example="email is required"),
     *                  @OA\Property(property="password", type="string", example="password is required"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Credential not match",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Credential not match"),
     *         )
     *     ),
     *
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => "ensure that all required filed are properly filled ",
                "data" => $validator->errors()
            ], 400);
        }

        $login = (new AuthService)->Login($request);
        if ($login) {
            return new LoginResource($login);
        }

        return response()->json([
            "status" => "error",
            "message" => "Credential not match",
        ], 401);
    }

    public function signup(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string',],
            'username' => ['required', 'string', 'unique:users,username'],
            'email' => ['required', 'string', 'unique:users,email'],
            'password' => ['required', 'string'],
        ]);

        // if (isset($request->ref)) {
        //     $validator = Validator::make($request->all(), [
        //         'ref' => ['required', 'string', 'exists:users,username'],
        //     ]);
        //     if ($validator->fails()) {
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => "All fields are required ",
        //             "data" => $validator->errors()
        //         ], 400);
        //     }
        //     $getRef = User::where('username', $request->ref)->first();
        //     $request->merge(['ref' => $getRef->id]);
        // }

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => "All fields are required ",
                "data" => $validator->errors()
            ], 400);
        }

        $register = (new userService)->Register($request);
        if ($register) {

            Mail::to($request->email)->send(new RegisterMail($register));

            return (new StoreUserResource($register))->additional([
                "status" => "success",
                "message" => "Register Successfully",

            ]);
        }
        return response()->json([
            "status" => "success",
            "message" => "Register Successfully",
        ], 400);
    }
    /**
     *  Staff create new password
     *
     * @OA\Post(
     *     path="/api/auth/store-password",
     *     tags={"Authentication"},
     *     summary="Staff Create Password",
     *     description="This allow the staff to create new password to  login to there account",
     *     operationId="createStaffPassword",
     *     @OA\Parameter(
     *         in="query",
     *         name="user",
     *         required=false,
     *         description="staff id",
     *         @OA\Schema(type="text")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="token",
     *         required=false,
     *         description="staff password token send to the email",
     *         @OA\Schema(type="text")
     *     ),   
     *     @OA\Parameter(
     *         in="query",
     *         name="password",
     *         required=false,
     *         description="Enter new password",
     *         @OA\Schema(type="password")
     *     ),   
     *     @OA\Parameter(
     *         in="query",
     *         name="password_confirmation",
     *         required=false,
     *         description="Confirm password",
     *         @OA\Schema(type="password")
     *     ),   
     *     @OA\Response(
     *         response="200",
     *         description="Password Created Successfull",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Password Created Successfull"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Invalid Token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid Password Token"),
     *         )
     *     ),
     *
     * )
     */

    public function storePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|confirmed|min:8',
            'user' => 'required',
            'token' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => "All fields are required ",
                "data" => $validator->errors()
            ], 403);
        }
        $verityEmail = ["email_verified_at" => Carbon::now()];
        $request->merge($verityEmail);
        $savePassword = (new UserService)->storePassword($request);

        if ($savePassword) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password Created successful'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong'
        ],  400);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     summary="Send a password reset email",
     *     description="Sends a password reset email to the user with the provided email address.",
     *     tags={"Authentication"},
     *      @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email address",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="email"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password Reset Email Sent Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Password Reset Email Sent Successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error or Something Went Wrong",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="All fields are required or Something went wrong"),
     *             
     *         )
     *     ),
     *    
     * )
     */
    public function forgotPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', "email"],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => "All fields are required ",
                "data" => $validator->errors()
            ], 400);
        }

        $forgotPassword = (new AuthService)->forgotPassword($request);

        if ($forgotPassword) {
            Mail::to($request->email)->send(new ForgotPasswordMail($forgotPassword));
            return response()->json([
                'status' => 'success',
                'message' => 'Password Reset Email Sent Successfully'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong'
        ],  400);
    }
}
