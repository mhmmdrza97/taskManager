<?php

namespace App\Http\Controllers\tasks;

use App\Http\Controllers\Controller;
use App\Models\task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class taskController extends Controller
{
    /**
     * Create New Task
     *
     * @OA\Post(
     *     path="/api/v1/create-task",
     *     tags={"Tasks"},
     *     summary="Create New Task",
     *     description="Submit new task for your self or anyone have been register in system",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password", "task_name", "description", "due_date", "assigned_to"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="task_name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="due_date", type="string"),
     *             @OA\Property(property="assigned_to", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success"),
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
    public function createTask(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'task_name' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required|string',
            'assigned_to' => 'required|string',
        ]);

        // پیدا کردن کاربر از روی username
        $user = User::where('email', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        if($request->assigned_to == $user->id){
            task::create([
                'task_name' => $request->task_name,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'assigned_to' => $user->id,
            ]);

            return response()->json([
                'message' => 'Success to create task'
            ], 200);
        }else{
            // بررسی نقش کاربر
            if ($user->role_id === 1) {
                task::create([
                    'task_name' => $request->task_name,
                    'description' => $request->description,
                    'due_date' => $request->due_date,
                    'assigned_to' => $request->assigned_to,
                ]);

                return response()->json([
                    'message' => 'Success to create task'
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Forbidden'
                ], 403);
            }
        }
    }

    /**
     * List Of Tasks
     *
     * @OA\Post(
     *     path="/api/v1/list-task",
     *     tags={"Tasks"},
     *     summary="List Of Task",
     *     description="Response List Of Tasks",
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
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="tasks", ref="#/components/schemas/Tasks")
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
    public function listTask(Request $request)
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
                'user' => task::all()
            ], 200);
        }else{
            return response()->json([
                'message' => 'Success',
                'user' => task::where('assigned_to', $user->id)->get()
            ], 200);
        }
    }

    /**
     * Update An Existing Task
     *
     * @OA\Post(
     *     path="/api/v1/update-status-task",
     *     tags={"Tasks"},
     *     summary="Update An Existing Task",
     *     description="Update an exsiting task for your self or anyone have been register in system",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password", "task_id", "task_name", "description", "due_date", "assigned_to", "status"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="task_id", type="string"),
     *             @OA\Property(property="task_name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="due_date", type="string"),
     *             @OA\Property(property="assigned_to", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task Not Found"
     *     )
     * )
     */
    public function updateTask(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'task_id' => 'required|string',
            'task_name' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required|string',
            'assigned_to' => 'required|string',
            'status' => 'required|string',
        ]);

        // پیدا کردن کاربر از روی username
        $user = User::where('email', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $task = task::where('id', $request->task_id)->first();
        if($task){
            if($task->assigned_to == $user->id){
                $task->update([
                                'task_name' => $request->task_name,
                                'description' => $request->description,
                                'due_date' => $request->due_date,
                                'status' => $request->status,
                              ]);
                return response()->json([
                    'message' => 'Success to update task'
                ], 200);
            }else{
                if ($user->role_id === 1) {
                    $task->update([
                        'task_name' => $request->task_name,
                        'description' => $request->description,
                        'due_date' => $request->due_date,
                        'assigned_to' => $request->assigned_to,
                        'status' => $request->status,
                    ]);
                    return response()->json([
                        'message' => 'Success to update task'
                    ], 200);
                }else{
                    return response()->json([
                        'message' => 'Forbidden'
                    ], 403);
                }
            }
        }else{
            return response()->json([
                'message' => 'Not Found!'
            ], 404);
        }
    }

    /**
     * Delete An Existing Task
     *
     * @OA\Post(
     *     path="/api/v1/delete-task",
     *     tags={"Tasks"},
     *     summary="Delete An Existing Task",
     *     description="Delete an exsiting task for your self or anyone have been register in system when not role is admin",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password", "task_id"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="task_id", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task Not Found"
     *     )
     * )
     */
    public function deleteTask(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'task_id' => 'required|string',
        ]);

        // پیدا کردن کاربر از روی username
        $user = User::where('email', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $task = task::where('id', $request->task_id)->first();
        if($task){
            if ($user->role_id === 1) {
                $task->delete();
                return response()->json([
                    'message' => 'Success to delete task'
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Forbidden'
                ], 403);
            }
        }else{
            return response()->json([
                'message' => 'Not Found!'
            ], 404);
        }
    }

    /**
     * Counter Of Tasks
     *
     * @OA\Post(
     *     path="/api/v1/counter-task",
     *     tags={"Tasks"},
     *     summary="Counter Of Task",
     *     description="Response Counter Of Tasks",
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
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="taskall", type="string", example="0"),
     *             @OA\Property(property="openTaskCount", type="string", example="0"),
     *             @OA\Property(property="closeTaskCount", type="string", example="0")
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
    public function counterTask(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // پیدا کردن کاربر از روی username
        $user = User::where('email', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        if ($user->role_id === 1) {
            $taskall = task::count();
            $openTaskCount = task::where('status', 0)->count();
            $closeTaskCount = task::where('status', 1)->count();
            return response()->json([
                'message' => 'Success to delete task',
                'taskall' => $taskall,
                'openTaskCount' => $openTaskCount,
                'closeTaskCount' => $closeTaskCount
            ], 200);
        }else{
            $taskall = task::where('assigned_to', $user->id)->count();
            $openTaskCount = task::where('assigned_to', $user->id)->where('status', 0)->count();
            $closeTaskCount = task::where('assigned_to', $user->id)->where('status', 1)->count();
            return response()->json([
                'message' => 'Success to delete task',
                'taskall' => $taskall,
                'openTaskCount' => $openTaskCount,
                'closeTaskCount' => $closeTaskCount
            ], 200);
        }
    }
}
