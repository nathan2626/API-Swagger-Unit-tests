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
}
