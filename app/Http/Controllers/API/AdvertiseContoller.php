<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdvertise;
use App\Http\Requests\UpdateAdvertise;
use App\Http\Resources\AppAdResource;
use App\Models\Advertise;
use Illuminate\Http\Request;
// use App\Events\RedisDataEvent;

class AdvertiseContoller extends Controller
{
    public function index()
    {
        $advertise = Advertise::get();
        return AppAdResource::collection($advertise);
    }

    public function store(CreateAdvertise $request)
    {
        return AppAdResource::make($request->persist());
    }

    public function show(Advertise $appAd)
    {
        return AppAdResource::make($appAd);
    }

    public function update(UpdateAdvertise $request, Advertise $appAd)
    {
        return AppAdResource::make($request->persist($appAd));
    }

    public function destroy(Advertise $appAd)
    {
        $appAd->delete();
        // call event
        // event(new RedisDataEvent());
        return response('Advertise Deleted Successfully');
    }
}
