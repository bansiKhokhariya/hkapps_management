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

    public function task_todo($task_id){
        $todoList = TodoList::where('task_id',$task_id)->get();
        return TodoListResource::collection($todoList);
    }
}
