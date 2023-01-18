<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePartyRequest;
use App\Http\Requests\UpdatePartyRequest;
use App\Http\Resources\PartyResource;
use App\Models\PartyMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartyMasterController extends Controller
{
    public function index()
    {
        $companyUser = Auth::user()->company_master_id;
        if (!$companyUser) {
            $PartyMaster = PartyMaster::all();
        } else {
            $PartyMaster = PartyMaster::where('company_master_id', $companyUser)->get();
        }
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
