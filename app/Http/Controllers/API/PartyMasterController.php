<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePartyRequest;
use App\Http\Requests\UpdatePartyRequest;
use App\Http\Resources\PartyResource;
use App\Models\PartyMaster;
use Illuminate\Http\Request;

class PartyMasterController extends Controller
{
    public function index()
    {
        $PartyMaster = PartyMaster::all();
        return PartyResource::collection($PartyMaster);
    }

    public function store(CreatePartyRequest $request)
    {
        return PartyResource::make($request->persist());
    }

    public function show(PartyMaster $PartyMaster)
    {
        return PartyResource::make($PartyMaster);
    }

    public function update(UpdatePartyRequest $request, PartyMaster $PartyMaster)
    {
        return PartyResource::make($request->persist($PartyMaster));
    }

    public function destroy(PartyMaster $PartyMaster)
    {

        $PartyMaster->delete();
        return response('PartyMaster Deleted Successfully');
    }
}
