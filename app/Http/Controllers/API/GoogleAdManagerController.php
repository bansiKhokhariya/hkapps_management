<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GoogleAdManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class GoogleAdManagerController extends Controller
{

    public function GoogleAdManagerSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jsonFilePath' => 'required|mimes:json',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors())->setStatusCode(422);
        } else {

            $file = $request->file('jsonFilePath');
            $path = Storage::disk('public')->putFile('GoogleAdManagerJsonFile', $request->file('jsonFilePath'));

//            $pathGet = Storage::disk('public')->get('GoogleAdManagerJsonFile/T7Be2MXmnMwIeHWcpMoX0eVfDeySKdXabkVAzB1X.json');

            $googleAdManager = new GoogleAdManager();
            $googleAdManager->name = $request->name;
            $googleAdManager->jsonFilePath = $path;
            $googleAdManager->save();

            return $googleAdManager;
        }

    }

    public function GoogleAdManagerGetAllNetwork()
    {

        $googleAdManager = GoogleAdManager::all();
        return $googleAdManager;

    }

    public function checkGoogleResponse($id)
    {
        $googleAdManager = GoogleAdManager::find($id);

        $data = [
            'id' => $googleAdManager->id,
            'name' => $googleAdManager->name,
            'jsonFilePath' => $googleAdManager->jsonFilePath,
            'advertise_id' => $googleAdManager->advertise_id,
            'order_id' => $googleAdManager->order_id,
            'trafficker_id' => $googleAdManager->trafficker_id,
            'web_property_code' => $googleAdManager->web_property_code,
            'placementId' => $googleAdManager->placementId,
            'lineItemId' => json_decode($googleAdManager->lineItemId),
            'currentNetworkCode' => $googleAdManager->currentNetworkCode,
            'created_at' => $googleAdManager->created_at,
            'updated_at' => $googleAdManager->updated_at,
        ];

        if ($googleAdManager->advertise_id) {
            return response()->json(['status' => true, 'data' => $data]);
        } else {
            return response()->json(['status' => false]);
        }
    }


}
