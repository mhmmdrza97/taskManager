<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Create Tasks",
 * )
 */

/**
 * @OA\Schema(
 *     schema="Tasks",
 *     required={"id", "task_name", "description", "assigned_to", "due_date", "status"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="User ID"
 *     ),
 *     @OA\Property(
 *         property="task_name",
 *         type="string",
 *         description="Task's name"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Task's description"
 *     ),
 *     @OA\Property(
 *          property="assigned_to",
 *          type="integer",
 *          description="Task's assigned_to"
 *     ),
 *     @OA\Property(
 *           property="due_date",
 *           type="string",
 *           description="Task's due_date"
 *     ),
 *     @OA\Property(
 *            property="status",
 *            type="boolean",
 *            description="Task's status"
 *     )
 * )
 */
class task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'task_name',
        'description',
        'due_date',
        'assigned_to',
        'status',
    ];
}
