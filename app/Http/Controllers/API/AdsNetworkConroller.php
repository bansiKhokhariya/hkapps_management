<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdsNetworkRequest;
use App\Http\Requests\UpdateAdsNetworkRequest;
use App\Http\Resources\AdsNetworkResource;
use App\Models\AdsNetwork;
use CreateAdsNetworkTable;
use Illuminate\Http\Request;

class AdsNetworkConroller extends Controller
{
    public function index()
    {
        $adsNetwork = AdsNetwork::all();
        return AdsNetworkResource::collection($adsNetwork);
    }

    public function store(CreateAdsNetworkRequest $request)
    {
        return AdsNetworkResource::make($request->persist());
    }

    public function show(AdsNetwork $adsNetwork)
    {
        return AdsNetworkResource::make($adsNetwork);
    }

    public function update(UpdateAdsNetworkRequest $request, AdsNetwork $adsNetwork)
    {
        return AdsNetworkResource::make($request->persist($adsNetwork));
    }

    public function destroy(AdsNetwork $adsNetwork)
    {

        $adsNetwork->delete();
        return response('Ads Network Deleted Successfully');
    }
}
