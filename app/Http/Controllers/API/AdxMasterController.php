<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdsRequest;
use App\Http\Requests\CreateAdxRequest;
use App\Http\Requests\UpdateAdxRequest;
use App\Http\Resources\AdxResource;
use App\Models\AdxMaster;
use Illuminate\Http\Request;

class AdxMasterController extends Controller
{
    public function index()
    {
        $adxMaster = AdxMaster::all();
        return AdxResource::collection($adxMaster);
    }

    public function store(CreateAdxRequest $request)
    {
        return AdxResource::make($request->persist());
    }

    public function show(AdxMaster $AdxMaster)
    {
        return AdxResource::make($AdxMaster);
    }

    public function update(UpdateAdxRequest $request, AdxMaster $AdxMaster)
    {
        return AdxResource::make($request->persist($AdxMaster));
    }

    public function destroy(AdxMaster $AdxMaster)
    {

        $AdxMaster->delete();
        return response('AdxMaster Deleted Successfully');
    }
}
