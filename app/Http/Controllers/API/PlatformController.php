<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePlatformRequest;
use App\Http\Requests\UpdatePlatformRequest;
use App\Http\Resources\PlatformResource;
use App\Models\PlatForm;
use Illuminate\Http\Request;
use App\Events\RedisDataEvent;

class PlatformController extends Controller
{
    public function index()
    {
        $platform = PlatForm::get();
        return PlatformResource::collection($platform);
    }

    public function store(CreatePlatformRequest $request)
    {
        return PlatformResource::make($request->persist());
    }

    public function show(PlatForm $platform)
    {
        return PlatformResource::make($platform);
    }

    public function update(UpdatePlatformRequest $request, PlatForm $platform)
    {
        return PlatformResource::make($request->persist($platform));
    }

    public function destroy(PlatForm $platform)
    {
        $platform->delete();
        // call event
        // event(new RedisDataEvent());
        return response('PlatForm Deleted Successfully');
    }
}
