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
        $tasksUser = auth()->user()->tasks()->orderBy('updated_at')->get();
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
        ];

        DB::table('tasks')->insert([
            'body' => $params['body'],
            'user_id' => auth()->user()->id,
        ]);

        return response()->json(['message'=>'Created'], 201);
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

        return response()->json($task, 200);

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

        DB::table('tasks')->insert([
            'body' => $params['body'],
            'done' => $params['done'],
            'user_id' => auth()->user()->id,
        ]);

        return response()->json(['message'=>'Updated'], 201);

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

        DB::table('tasks')->where('id', '=',  $id)->delete();

        return response()->json(["message"=>"Tache supprimée"], 200);

    }
}
