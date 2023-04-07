<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAllConsoleRequest;
use App\Http\Requests\UpdateAllConsoleRequest;
use App\Http\Resources\AllConsoleResource;
use App\Models\AllConsole;
use Illuminate\Http\Request;

class AllConsoleController extends Controller
{
    public function index()
    {
        $allConsole = AllConsole::all();
        return AllConsoleResource::collection($allConsole);
    }

    public function store(CreateAllConsoleRequest $request)
    {
        return AllConsoleResource::make($request->persist());
    }

    public function show(AllConsole $allConsole)
    {
        return AllConsoleResource::make($allConsole);
    }

    public function update(UpdateAllConsoleRequest $request, AllConsole $allConsole)
    {
        return AllConsoleResource::make($request->persist($allConsole));
    }

    public function destroy(AllConsole $allConsole)
    {
        $allConsole->delete();
        return response('Console Deleted Successfully');
    }
}
