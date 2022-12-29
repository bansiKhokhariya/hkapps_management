<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdsRequest;
use App\Http\Requests\UpdateAdsRequest;
use App\Http\Resources\AdsResource;
use App\Models\AdsMaster;
use Illuminate\Http\Request;

class AdsMasterController extends Controller
{
    public function index()
    {
        $adsMaster = AdsMaster::all();
        return AdsResource::collection($adsMaster);
    }

    public function store(CreateAdsRequest $request)
    {
        return AdsResource::make($request->persist());
    }

    public function show(AdsMaster $adsMaster)
    {
        return AdsResource::make($adsMaster);
    }

    public function update(UpdateAdsRequest $request, AdsMaster $adsMaster)
    {
        return AdsResource::make($request->persist($adsMaster));
    }

    public function destroy(AdsMaster $adsMaster)
    {

        $adsMaster->delete();
        return response('AdsMaster Deleted Successfully');
    }
}
