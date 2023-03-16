<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GooglePlayApiController extends Controller
{

    public function GetGooglePLayAppById($id)
    {

        $getGooglePLayAppById = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/apps/' . $id);

        $getGooglePLayAppByIdRes = $getGooglePLayAppById->json();

        return $getGooglePLayAppByIdRes;

    }

    public function SearchGooglePlayAppsByQuery(Request $request)
    {

        $q = $request->query('q');
        $published = $request->query('published');
        $country = $request->query('country');
        $fields = $request->query('fields');
        $limit = $request->query('limit');
        $page = $request->query('page');
        $sort = $request->query('sort');

        $searchGooglePlayAppsByQuery = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/apps', [
            'q' => $q,
            'published' => $published,
            'country' => $country,
            'fields' => $fields,
            'limit' => $limit,
            'page' => $page,
            'sort' => $sort,
        ]);
        $response = $searchGooglePlayAppsByQuery->json();

        return $response;

    }


    public function GetGooglePlayAppAvailableCountry(){

        $response = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/info/countries');

        $getCountry = $response->json();

        return $getCountry;
    }

    public function SearchGooglePlayAppsByQueryPost(Request $request){

        $q = $request->q;
        $limit = $request->limit;
        $page = $request->page;
        $sort = $request->sort;
        $fields = $request->fields;
        $country = $request->country;

        $searchGooglePlayAppsByQuery = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->post('https://api.appstorespy.com/v1/play/apps', [
            'q' => $q,
            'limit' => $limit,
            'page' => $page,
            'sort' => $sort,
            'fields' => $fields,
            'country' => $country,
        ]);


        $response = $searchGooglePlayAppsByQuery->json();
        return $response;

    }



}
