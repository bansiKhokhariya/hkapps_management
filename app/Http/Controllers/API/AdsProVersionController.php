<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdsProVersion;
use Illuminate\Http\Request;

class AdsProVersionController extends Controller
{
    public function index()
    {
        $adsProVersion  = AdsProVersion::all();
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
}
