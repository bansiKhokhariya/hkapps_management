<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTodoListRequest;
use App\Http\Requests\UpdateTodoListRequest;
use App\Http\Resources\TodoListResource;
use App\Models\DefaultTodo;
use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\Request;

class TodoListController extends Controller
{
    public function index()
    {
        $todoList = TodoList::all();
        return TodoListResource::collection($todoList);
    }

    public function store(CreateTodoListRequest $request)
    {
        return TodoListResource::make($request->persist());
    }

    public function show(TodoList $todoList)
    {
        return TodoListResource::make($todoList);
    }

    public function update(UpdateTodoListRequest $request, TodoList $todoList)
    {
        return TodoListResource::make($request->persist($todoList));
    }

    public function destroy(TodoList $todoList)
    {
        $todoList->delete();
        return response('Todo List Deleted Successfully');
    }

    public function task_todo($task_id)
    {

        $todoList = TodoList::where('task_id', $task_id)->with('task')->get();
        $designerTodo = TodoList::where('task_id', $task_id)->where('category', 'designer')->with('task')->get();
        $developerTodo = TodoList::where('task_id', $task_id)->where('category', 'developer')->with('task')->get();
        $testerTodo = TodoList::where('task_id', $task_id)->where('category', 'tester')->with('task')->get();
//        $defaultDesignerTodo = DefaultTodo::where('task_id', null)->where('category', 'designer')->get();
//        $defaultDeveloperTodo = DefaultTodo::where('task_id', null)->where('category', 'developer')->get();
//        $defaultTesterTodo = DefaultTodo::where('task_id', null)->where('category', 'tester')->get();

//        $developerTodoList = $developerTodo->concat($defaultDeveloperTodo);
//        $designerTodoList = $designerTodo->concat($defaultDesignerTodo);
//        $testerTodoList = $testerTodo->concat($defaultTesterTodo);

        return response()->json(['generalTodo' => $todoList, 'designerTodo' => $designerTodo, 'developerTodo' => $developerTodo, 'testerTodo' => $testerTodo]);

//        return TodoListResource::collection($todoList);

    }

    public function addDefaultTodo(Request $request)
    {
        $request->validate([
            'todoName' => 'required',
            'category' => 'required',
        ]);

        $defaultTodo = new DefaultTodo();
        $defaultTodo->todoName = $request->todoName;
        $defaultTodo->category = $request->category;
        $defaultTodo->save();

        $task = Task::all();
        if ($task) {
            foreach ($task as $taskId) {
                $todo = TodoList::where('task_id', $taskId->id)->where('category', $defaultTodo->category)->where('todoName', $defaultTodo->todoName)->first();
                if (!$todo) {
                    $todoList = new TodoList();
                    $todoList->task_id = $taskId->id;
                    $todoList->todoName = $request->todoName;
                    $todoList->category = $request->category;
                    $todoList->completed = 'false';
                    $todoList->save();
                }
            }
        }


        return $defaultTodo;

    }

    public function getDefaultTodo()
    {

        $designerTodo = DefaultTodo::where('category', 'designer')->get();
        $developerTodo = DefaultTodo::where('category', 'developer')->get();
        $testerTodo = DefaultTodo::where('category', 'tester')->get();

        return response()->json(['designerTodo' => $designerTodo, 'developerTodo' => $developerTodo, 'testerTodo' => $testerTodo]);

    }


}
