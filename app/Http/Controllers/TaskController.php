<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {

//        if(!auth()->user()){
//            return response()->json([
//                'error'=> "Vous n'êtes pas co"
//            ], 401);
//        }
        $tasksUser = auth()->user()->tasks()->orderBy('updated_at', 'desc')->get();
        return response()->json([
            'tasks'=>$tasksUser
        ], 201);

    }

    public function store(Request $request)
    {

        $request->validate([
            'body' => 'required',
        ]);

        if(!$request->user()->tokenCan('tasks:write')) {
            return response()->json(['message'=> 'You dont have the ability to do that.'], 401);
        }

        $params = [
            'body' => $request->get('body'),
            'done' => $request->get('done'),
        ];

        $task = Task::create([
            'body' => $params['body'],
            'user_id' => auth()->user()->id,
            'done' => $params['done']
        ]);
        $task->save();
        return response()->json(['message'=>'La tâche à bien été créée.', 'task' => $task], 201);
    }

    public function show(Request $request, $id)
    {
        $task = Task::find($id);

        if(!$task) {
            return response()->json(["message" => "La tâche n'existe pas"],404);
        }

        if($task->user->id != $request->user()->id) {
            return response()->json(["message"=>"Accès à la tâche non autorisé"], 403);
        }

        return response()->json(['task' => $task], 200);

    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if(!$task) {
            return response()->json(["message" => "La tâche n'existe pas"], 404);
        }

        if($task->user->id != $request->user()->id) {
            return response()->json(["message"=>"Accès à la tâche non autorisé"], 403);
        }

        $request->validate([
            'body' => 'required',
            'done' => 'required'
        ]);

        if(!$request->user()->tokenCan('tasks:write')) {
            return response()->json(['message'=> 'You dont have the ability to do that.'], 401);
        }

        $params = [
            'body' => $request->get('body'),
            'done' => $request->get('done')
        ];


        $task = Task::where('id', $id)->update([
            'body' => $params['body'],
            'user_id' => auth()->user()->id,
            'done' => $params['done']
        ]);
//        $task->save();

//        DB::table('tasks')->insert([
//            'body' => $params['body'],
//            'done' => $params['done'],
//            'user_id' => auth()->user()->id,
//        ]);

        return response()->json(['message'=>'La tâche à été modifiée.', 'task' => $task], 200);

    }

    public function destroy(Request $request, $id)
    {
        $task = Task::find($id);

        if(!$task) {
            return response()->json(["message" => "La tâche n'existe pas"], 404);
        }

        if($task->user->id != $request->user()->id) {
            return response()->json(["message"=>"Accès à la tâche non autorisé"], 403);
        }

        Task::where('id', $id)->delete();

        // return response()->json(["message"=>"Tache supprimée", 'task'=> $task->id], 200);
        return response()->json(["message"=>"Tache supprimée"], 204);


    }

     /**
     * @OA\Get(path="/api/tasks",
     *   tags={"tasks"},
     *   summary="Tasks user",
     *   description="Tasks user",
     *   operationId="allTasksUser",
     *   security={ {"bearerAuth": {}} },
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="name", type="string", example="Tolo tolo to lo tolotolotolo tolooooo toloooo otltolotlto"),  
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Unauthorized"),
    *        )
    *     ),
     * )
     */

       /**
     * @OA\Get(path="/api/tasks{id}",
     *   tags={"tasks"},
     *   summary="Task user",
     *   description="Show task user",
     *   operationId="showTaskwUser",
     *   security={ {"bearerAuth": {}} },
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="name", type="string", example="Tolo tolo to lo tolotolotolo tolooooo toloooo otltolotlto"),  
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Unauthorized"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=404,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="La tâche n'existe pas"),
    *        )
    *     ),
     * )
     */

     /**
     * @OA\Post(path="/api/tasks",
     *   summary="Create task",
     *   tags={"tasks"},
     *   description="Create task",
     *   operationId="createTask",
     *   security={ {"bearerAuth": {}} },
     * @OA\RequestBody(
    *    required=true,
    *    description="Body",
    *    @OA\JsonContent(
    *       required={"body"},
    *       @OA\Property(property="body", type="string", example="Tolo tolo to lo tolotolotolo tolooooo toloooo otltolotlto"),
    *    ),
    * ),
     *  @OA\Response(
    *    response=201,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="name", type="string", example="Tolo tolo to lo tolotolotolo tolooooo toloooo otltolotlto"), 
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Please fill in all fields"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Unauthorized"),
    *        )
    *     ),
     * )
     */

     /**
     * @OA\Delete(path="/api/tasks/{id}",
     *   tags={"tasks"},
     *   summary="Delete task user",
     *   description="Delete task user",
     *   operationId="deleteTasksUser",
     *   security={ {"bearerAuth": {}} },
     * @OA\Parameter(
    *    description="ID of task",
    *    in="path",
    *    name="id",
    *    required=true,
    *    example="1",
    *    @OA\Schema(
    *       type="integer",
    *       format="int64",
    *    )
    * ),
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="success", type="string", example="Tache supprimée"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=403,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Accès à la tâche non autorisé"),
    *        )
    *     ),
    *   @OA\Response(
    *    response=404,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="La tâche n'existe pas"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Unauthorized"),
    *        )
    *     ),
     * )
     */

      /**
     * @OA\Put(path="/api/tasks/{id}",
     *   tags={"tasks"},
     *   summary="Update task user",
     *   description="Update task user",
     *   operationId="updateTasksUser",
     *   security={ {"bearerAuth": {}} },
     *   @OA\RequestBody(
    *    required=true,
    *    description="Body",
    *    @OA\JsonContent(
    *       required={"body"},
    *       @OA\Property(property="body", type="string", example="Tolo tolo to lo tolotolotolo tolooooo toloooo otltolotlto"),
    *    ),
    * ),
     * @OA\Parameter(
    *    description="ID of task",
    *    in="path",
    *    name="id",
    *    required=true,
    *    example="1",
    *    @OA\Schema(
    *       type="integer",
    *       format="int64",
    *    )
    * ),
    *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="name", type="string", example="Tolo tolo to lo tolotolotolo tolooooo toloooo otltolotlto"), 
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Please fill in all fields"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=404,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="La tâche n'existe pas"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=403,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Accès à la tâche non autorisé"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Unauthorized"),
    *        )
    *     ),
     * )
     */
    

}


