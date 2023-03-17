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

    public function SearchGooglePlayAppsByQueryPost(Request $request)
    {

        $q = $request->q;
        $limit = $request->limit;
        $page = $request->page;
        $sort = $request->sort;
        $fields = $request->fields;
        $country = $request->country;


//        $searchGooglePlayAppsByQuery = Http::withHeaders([
//            'accept' => "application/json",
//            'API-KEY' => config('global.API-KEY')
//        ])->post('https://api.appstorespy.com/v1/play/apps', [
//            'q' => $q,
//            'limit' => $limit,
//            'page' => $page,
//            'sort' => $sort,
//            'fields' => $fields,
//            'country' => $country,
//        ]);

        $searchGooglePlayAppsByQuery = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->post('https://api.appstorespy.com/v1/play/apps', ['body' =>
            [
                'q' => $q,
                'limit' => $limit,
                'page' => $page,
                'sort' => $sort,
                'fields' => $fields,
                'country' => $country,
            ]
        ]);

        dd($searchGooglePlayAppsByQuery);

        $response = $searchGooglePlayAppsByQuery->json();
        return $response;

    }

    public function appReview(Request $request, $id)
    {

        $country = $request->query('country');
        $language = $request->query('language');
        $limit = $request->query('limit');
        $sort = $request->query('sort');

        $appReview = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/apps/' . $id . '/reviews', [
            'language' => $language,
            'country' => $country,
            'limit' => $limit,
            'sort' => $sort,
        ]);

        $response = $appReview->json();

        return $response;

    }

    public function GetGooglePlayAppAvailableCountry()
    {

        $response = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/info/countries');

        $getCountry = $response->json();

        return $getCountry;
    }

    public function GetGooglePlayAppAvailableLanguage()
    {

        $response = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/info/languages');

        $getLanguages = $response->json();

        return $getLanguages;
    }

    public function getDeveloper(Request $request, $id)
    {
        $fields = $request->query('fields');
        $getDeveloper = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/developers/' . $id, [
            'fields' => $fields,
        ]);

        $response = $getDeveloper->json();

        return $response;

    }

    public function devSearch(Request $request)
    {

        $q = $request->query('q');
        $fields = $request->query('fields');
        $limit = $request->query('limit');
        $page = $request->query('page');
        $sort = $request->query('sort');

        $devSearch = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/developers', [
            'q' => $q,
            'fields' => $fields,
            'limit' => $limit,
            'page' => $page,
            'sort' => $sort,
        ]);
        $response = $devSearch->json();

        return $response;
    }

    public function getAppsEsimates(Request $request)
    {

        $id = $request->query('id');
        $start = $request->query('start');
        $end = $request->query('end');


        $devSearch = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/esimates', [
            'id' => $id,
            'start' => $start,
            'end' => $end,
        ]);
        $response = $devSearch->json();

        return $response;
    }

    public function getSuggest(Request $request)
    {

        $q = $request->query('q');
        $country = $request->query('country');
        $lang = $request->query('lang');
        $freshness = $request->query('freshness');


        $getSuggest = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/suggestions', [
            'q' => $q,
            'country' => $country,
            'lang' => $lang,
            'freshness' => $freshness,
        ]);

        $response = $getSuggest->json();

        return $response;
    }

    public function getEvents(Request $request)
    {

        $app = $request->query('app');
        $country = $request->query('country');
        $freshness = $request->query('freshness');


        $getEvents = Http::withHeaders([
            'accept' => "application/json",
            'API-KEY' => config('global.API-KEY')
        ])->get('https://api.appstorespy.com/v1/play/liveops', [
            'app' => $app,
            'country' => $country,
            'freshness' => $freshness,
        ]);

        $response = $getEvents->json();

        return $response;
    }


}
