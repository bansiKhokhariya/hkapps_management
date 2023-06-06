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
//        $advertise = Advertise::get();
//        return AppAdResource::collection($advertise);

        $data = [];
        $obj1 = (object)[
            "ad_id" => 1,
            "app_name" => "My photo phone dialer",
            "app_packageName" => "com.judi.dialcolor",
            "app_logo" => "http://panel.goldadx.com/app_logo/unnamed.webp",
            "app_banner" => "http://panel.goldadx.com/app_banner/Tasty-Food-Web-Banner-Design-1180x664.jpg",
            "app_shortDecription" => "🔥My Photo Phone Dialer, call my photos is app personalize your calling dial screen. Place a photo of your choice to make a dial screen of your own. Customize it the way you want it and stop using the boring phone's calling dial pads.\r\n🔥Replace boring calling dial pads with your photo or beauty live background.",
            "app_buttonName" => "install",
            "app_rating" => "2.3",
            "app_download" => "500K",
            "app_AdFormat" => [
                "Banner"
            ],
            "created_at" => "16-11-2022",
            "updated_at" => "16-11-2022",
        ];
        $obj2 = (object)[
            "ad_id" => 2,
            "app_name" => "MX Player: Videos, OTT & Games",
            "app_packageName" => "com.mxtech.videoplayer.ad",
            "app_logo" => "http://panel.goldadx.com/app_logo/logo.webp",
            "app_banner" => "https://www.google.co.in/",
            "app_shortDecription" => "🔥My Photo Phone Dialer, call my photos is app personalize your calling dial screen. Place a photo of your choice to make a dial screen of your own. Customize it the way you want it and stop using the boring phone's calling dial pads.\r\n🔥Replace boring calling dial pads with your photo or beauty live background.",
            "app_buttonName" => "app install",
            "app_rating" => "4.5",
            "app_download" => "100K",
            "app_AdFormat" => [
                "NativeBanner"
            ],
            "created_at" => "16-11-2022",
            "updated_at" => "16-11-2022",
        ];
        $finalArray = [];
        array_push($data,$obj1,$obj2);
        $response = array_merge($finalArray, ['data' => $data]);
        return $response;

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
