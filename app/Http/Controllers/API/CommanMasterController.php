<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCommanMasterRequest;
use App\Http\Requests\UpdateCommanMasterRequest;
use App\Http\Resources\CommanMasterResource;
use App\Models\CommanMaster;
use Illuminate\Http\Request;

class CommanMasterController extends Controller
{
    public function index()
    {
        $commanMaster = CommanMaster::all();
        return CommanMasterResource::collection($commanMaster);
    }

    public function store(CreateCommanMasterRequest $request)
    {
        return CommanMasterResource::make($request->persist());
    }

    public function show(CommanMaster $commanMaster)
    {
        return CommanMasterResource::make($commanMaster);
    }

    public function update(UpdateCommanMasterRequest $request, CommanMaster $commanMaster)
    {
        return CommanMasterResource::make($request->persist($commanMaster));
    }

    public function destroy(CommanMaster $commanMaster)
    {
        $commanMaster->delete();
        return response('Comman Master Deleted Successfully');
    }
}
