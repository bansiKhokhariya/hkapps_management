<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdsProVersion;
use App\Models\AllApps;
use App\Models\ApikeyList;
use Illuminate\Http\Request;

class AdsProVersionController extends Controller
{
    public function index()
    {
        $adsProVersion = AdsProVersion::all();
        return $adsProVersion;
    }

    public function store(Request $request)
    {

        $adsProVersion = AdsProVersion::find(1);

        if (!is_null($adsProVersion)) {
            $adsProVersion->update([
                'adsProVersion' => $request->adsProVersion,
            ]);
            return $adsProVersion;
        } else {
            $adsPro = new AdsProVersion();
            $adsPro->adsProVersion = $request->adsProVersion;
            $adsPro->save();
            return $adsPro;
        }

    }

    public function show()
    {

        $adsProVersion = AdsProVersion::find(1);
        return $adsProVersion;

    }


    public function adsProVersion(Request $request)
    {
        $package_name = $request->header('packageName');
        $api_key = $request->header('apiKey');

        $adsProVersion = AdsProVersion::find(1);
        $get_allApps = AllApps::where('app_packageName', $package_name)->first();
        $meta_keywords = explode(',', $get_allApps->app_apikey);
        if (!in_array($api_key, $meta_keywords)) {
            $get_api_key = ApikeyList::where('apikey_packageName', $package_name)->where('apikey_text', $api_key)->first();
            if ($get_api_key) {
                $apikey_request_count = $get_api_key->apikey_request;
                $apiKey = ApikeyList::find($get_api_key->id);
                $apiKey->apikey_request = $apikey_request_count + 1;
                $apiKey->save();
            } else {
                $apiKey = new ApikeyList();
                $apiKey->apikey_packageName = $package_name;
                $apiKey->apikey_text = $api_key;
                $apiKey->save();
            }
            return response()->json(['MSG' => 'App Not Found!']);
        } else {
            return $adsProVersion;
        }

    }
}
