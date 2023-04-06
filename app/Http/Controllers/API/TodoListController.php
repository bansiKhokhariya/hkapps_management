<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTodoListRequest;
use App\Http\Requests\UpdateTodoListRequest;
use App\Http\Resources\TodoListResource;
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
        $defaultDesignerTodo = TodoList::where('task_id', null)->where('category', 'designer')->get();
        $defaultDeveloperTodo = TodoList::where('task_id', null)->where('category', 'developer')->get();
        $defaultTesterTodo = TodoList::where('task_id', null)->where('category', 'tester')->get();

        $designerTodoList = (object)array_merge((array)$designerTodo, $defaultDesignerTodo);
        $developerTodoList = (object)array_merge((array)$developerTodo, $defaultDeveloperTodo);
        $testerTodoList = (object)array_merge((array)$testerTodo, $defaultTesterTodo);

        return response()->json(['generalTodo' => $todoList, 'designerTodo' => $designerTodoList, 'developerTodo' => $developerTodoList, 'testerTodo' => $testerTodoList]);

//        return TodoListResource::collection($todoList);

    }

    public function addDefaultTodo(Request $request)
    {
        $request->validate([
            'todoName' => 'required',
            'category' => 'required',
        ]);

        $todoList = new TodoList();
        $todoList->todoName = $request->todoName;
        $todoList->category = $request->category;
        $todoList->save();

        return $todoList;

    }

    public function getDefaultTodo(){

        $designerTodo = TodoList::where('task_id', null)->where('category', 'designer')->get();
        $developerTodo = TodoList::where('task_id', null)->where('category', 'developer')->get();
        $testerTodo = TodoList::where('task_id', null)->where('category', 'tester')->get();

        return response()->json(['designerTodo' => $designerTodo, 'developerTodo' => $developerTodo, 'testerTodo' => $testerTodo]);
    }


}
