<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\RegisterMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Service\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\ShowUserResource;
use App\Http\Resources\StoreUserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    private $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * List all Users
     * @OA\GET (
     *     path="/api/staff",
     *     tags={"Staff"},
     *     summary="Get all staffs",
     *     description="Show list of all staffs",
     *     operationId="getStaffs",
     *     security={{"api_key":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of all staffs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/StoreUserResource")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No User found"),
     *         )
     *     ),
     * )
     */
    public function index()
    {
        $user = $this->userService->viewAllStaff();

        if ($user) {
            return StoreUserResource::collection($user)->additional([
                "status" => "success",
            ]);
        }

        return response()->json([
            "status" => "error",
            "message" => "No Staff found",
        ], 404);
    }

    /**
     * @OA\Post(
     *     path="/api/staff",
     *     tags={"Staff"},
     *     summary="Register New Staff",
     *     description="This allow the MD to create new staff member",
     *     operationId="registerStaff",
     *     security={{"api_key":{}}},  
     *     @OA\RequestBody(
     *          description="Input staff details to register",
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *          type="object",
     *          required={"name","email", "phone", "zone", "role_id"},
     *          @OA\Property(property="name", description="Staff Fullname", example="Revenue Hub", type="text"),
     *          @OA\Property(property="email", description="Staff Email", type="text", example=""),
     *          @OA\Property(property="phone", description="Staff Phone", type="text", example=""),
     *          @OA\Property(property="zone", description="Staff Zone", type="text", example=""),
     *          @OA\Property(property="role_id", description="Staff Roles", type="integer", example=""),
     *          ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Registeration Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Register Successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=9),
     *                 @OA\Property(property="name", type="string", example="abc example2.com"),
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
     *        ),
     *     ),
     *
     * )
     */
    public function store(StoreUserRequest $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string'],
            'phone' => ['required', 'string', 'min:11'],
            'role_id' => ['required', 'string'],
            'zone' => ['required', 'string', 'max:255'],
        ]);

        $rememberToken = ["remember_token" => Str::random(60)];
        $request->merge($rememberToken);

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
     * Show  User
     * @OA\GET (
     *     path="/api/staff/{staff}",
     *     tags={"Staff"},
     *     summary="Get a staff",
     *     description="Show details of a staff",
     *     operationId="getStaff",
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="staff",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show details of a staff",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ShowUserResource")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="You dont Have Permission",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You dont Have Permission"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No Staff found"),
     *         )
     *     ),
     * )
     */
    public function show($staff)
    {
        $specificStaff = $this->userService->viewStaff($staff);
        if ($specificStaff) {
            return (new StoreUserResource($specificStaff))->additional([
                "status" => "success",
            ]);
        }

        return response()->json([
            "status" => "error",
            "message" => "No Staff Found",
        ], 404);
    }

    /**
     * @OA\Post(
     *     path="/api/user-with-token/{staff}",
     *     summary="Get User with Token",
     *     description="Fetches a user by their remember token and staff ID",
     *     operationId="getUserWithToken",
     *     tags={"Staff"},
     *     @OA\Parameter(
     *         name="staff",
     *         in="path",
     *         required=true,
     *         description="ID of the staff",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 description="Remember token of the user",
     *                 example="your_remember_token_here"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/StoreUserResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No Staff Found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No Staff Found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="You don't have permission",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="You don't have permission"
     *             )
     *         )
     *     )
     * )
     */
    public function getUserWithToken(Request $request, $staff)
    {
        $user = User::where('remember_token', $request->token)->first();
        if ($user && $user->id == $staff) {
            return (new StoreUserResource($user))->additional([
                "status" => "success",
            ]);
        }

        return response()->json([
            "status" => "error",
            "message" => "No Staff Found",
        ], 404);
    }



    /**
     * @OA\PUT(
     *     path="/api/staff/{staff}",
     *     tags={"Staff"},
     *     summary="Update Staff Details",
     *     description="This allow staff member to update their details",
     *     operationId="updateStaff",
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="staff",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ), 
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="phone",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="zone",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "name":"example name",
     *                     "email":"example email",
     *                     "phone":"example phone",
     *                     "zone":"example zone"
     *                }
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="Update Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Update Successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=9),
     *                 @OA\Property(property="name", type="string", example="abc kel"),
     *                 @OA\Property(property="email", type="string", example="abc@example2.com"),
     *                 @OA\Property(property="phone", type="string", example="65728338352"),
     *                 @OA\Property(property="zone", type="string", example="nigeria"),
     *                 @OA\Property(property="role", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="Admin"),
     *                 ),
     *             ),
     *        ),
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="All Fields are Required",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="All Fields are required"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="name", type="string", example="name is required"),
     *                  @OA\Property(property="email", type="string", example="email is required"),
     *                  @OA\Property(property="phone", type="string", example="phone is required"),
     *                  @OA\Property(property="zone", type="string", example="zone is required"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Credential error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Credential error: You are not authorize"),
     *         )
     *     ),
     *
     * )
     *
     *    @OA\PUT(
     *     path="/api/update-staff-details/{staff}",
     *     tags={"Staff"},
     *     summary="Update Staff Details",
     *     description="This allow staff member to update their details",
     *     operationId="updateStaffDetails",
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="staff",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ), 
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="phone",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="zone",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="remember_token",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "name":"example name",
     *                     "email":"example email",
     *                     "phone":"example phone",
     *                     "zone":"example zone"
     *                }
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="Update Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Update Successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=9),
     *                 @OA\Property(property="name", type="string", example="abc kel"),
     *                 @OA\Property(property="email", type="string", example="abc@example2.com"),
     *                 @OA\Property(property="phone", type="string", example="65728338352"),
     *                 @OA\Property(property="zone", type="string", example="nigeria"),
     *                 @OA\Property(property="role", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="Admin"),
     *                 ),
     *             ),
     *        ),
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="All Fields are Required",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="All Fields are required"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="name", type="string", example="name is required"),
     *                  @OA\Property(property="email", type="string", example="email is required"),
     *                  @OA\Property(property="phone", type="string", example="phone is required"),
     *                  @OA\Property(property="zone", type="string", example="zone is required"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Credential error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Credential error: You are not authorize"),
     *         )
     *     ),
     *
     * )
     */

    public function update(Request $request, $staff)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', "email"],
            'password' => ['sometimes', 'string', 'max:255'],
        ]);
        $getUserId = 0;


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => "All fields are required ",
                "data" => $validator->errors()
            ], 400);
        }



        if ($request->remember_token) {
            $getUserId = User::where('remember_token', $request->remember_token)->first()->id;
        }

        if (!empty($request->password)) {
            $password = ["password" => Hash::make("$request->password")];
            $request->merge($password);
        }

        $update = (new userService)->updateStaff($request, $staff);
        if ($update) {
            return response()->json([
                'status' => 'success',
                'message' => "Update Successfully",
            ], 200);
        }
        return response()->json([
            "status" => "error",
            "message" => "Credential error: You are not authorize",
        ], 401);
    }

    /**
     * @OA\Delete (
     *     path="/api/staff/{staff}",
     *     tags={"Staff"},
     *     summary="Delete a staff",
     *     description="This allow staff admin to delete staff",
     *     operationId="deleteStaff",
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="staff",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     
     *     @OA\Response(
     *         response=200,
     *         description="Staff deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Staff deleted successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="You don`t Have Permission",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You dont Have Permission"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="402",
     *         description="An error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="An error occured"),
     *         )
     *     ),
     * )
     */
    public function destroy($user)
    {
        if ($this->userService->deleteStaff($user)) {
            return response()->json([
                "status" => "success",
                "message" => "Staff deleted successfully",
            ], 200);
        }

        return response()->json([
            "status" => "error",
            "message" => "An error occurred",
        ], 402);
    }
}
