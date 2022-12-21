<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdvertise;
use App\Http\Requests\UpdateAdvertise;
use App\Http\Resources\AdvertiseResource;
use App\Models\Advertise;
use Illuminate\Http\Request;

class AdvertiseContoller extends Controller
{

    public function index()
    {
        $advertise = Advertise::get();
        return AdvertiseResource::collection($advertise);
    }

    public function store(CreateAdvertise $request)
    {
        return AdvertiseResource::make($request->persist());
    }

    public function show(Advertise $advertise)
    {
        return AdvertiseResource::make($advertise);
    }

    public function update(UpdateAdvertise $request, Advertise $advertise)
    {
        return AdvertiseResource::make($request->persist($advertise));
    }

    public function destroy(Advertise $advertise)
    {
        $advertise->delete();
        return response('Advertise Deleted Successfully');
    }
}
