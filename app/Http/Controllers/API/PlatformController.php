<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePlatformRequest;
use App\Http\Requests\UpdatePlatformRequest;
use App\Http\Resources\PlatformResource;
use App\Models\PlatForm;
use Illuminate\Http\Request;
use App\Events\RedisDataEvent;
use Illuminate\Support\Facades\Auth;

class PlatformController extends Controller
{
    public function index()
    {
        $companyUser = Auth::user()->company_master_id;
        if (!$companyUser) {
            $platform = PlatForm::get();
        } else {
            $platform = PlatForm::where('company_master_id', $companyUser)->get();
        }
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
