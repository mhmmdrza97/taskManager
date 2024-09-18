<?php

namespace App\Http\Controllers\Api\users;

use App\Http\Controllers\Controller;
use App\Models\task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(
 *     title="My Awesome API",
 *     version="1.0.0",
 *     description="This is the API documentation for My Awesome Project.",
 *     @OA\Contact(
 *         email="support@myawesomeapi.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 */

class userController extends Controller
{
    /**
     * User
     *
     * @OA\Post(
     *     path="/api/v1/usersList",
     *     tags={"Auth"},
     *     summary="List Of Users",
     *     description="Authenticate user with username and password, and return data if role is 1",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User role is 1",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function list(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // پیدا کردن کاربر از روی username
        $user = User::where('email', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // بررسی نقش کاربر
        if ($user->role_id === 1) {
            return response()->json([
                'message' => 'Success',
                'user' => User::with('role')->with('tasks')->get()
            ], 200);
        }else{
            return response()->json([
                'message' => 'Success',
                'user' => $user
            ], 200);
        }
    }
}
