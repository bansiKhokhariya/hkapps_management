<?php

namespace App\Http\Controllers\API;

use App\Events\RedisDataEvent;
use App\Http\Controllers\Controller;
use App\Models\AllAppsHistory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class RawDataController extends Controller
{


    public function storeRedisData($cursor = null)
    {
        // for event
        $id = Auth::user()->id;
        $auth_user = User::find($id);
        //

        if ($cursor === null) {
            $cursor = 0;
        }
        $redis = Redis::connection('RedisApp14');
        $arList = $redis->scan($cursor, ['count' => 100000, 'match' => '*']);
//        $arList = $redis->keys('*');
        $newArrayList = array_map(function ($item) {
            $values = explode('-', $item);
            $values = array_pad($values, 4, "");

            $headers = ['uniqueId', 'package', 'countryCode', 'ip'];
            $rawData = array_combine($headers, $values);
            return $rawData;
        }, $arList[1]);


        $chunks = array_chunk($newArrayList, 10000);
        foreach ($chunks as $chunk) {
            $getdata = AllAppsHistory::where('uniqueId', $chunk[0]['uniqueId'])->where('package', $chunk[0]['package'])->where('countryCode', $chunk[0]['countryCode'])->where('ip', $chunk[0]['ip'])->first();
            if (!$getdata) {
                AllAppsHistory::insert($chunk);
            }
        }

        //event call
        event(new RedisDataEvent($arList[0],$auth_user));


        return response()->json(['message' => 'data added successfully', 'data' => $arList[0]]);

    }


//    public function GetRedisData($cursor = null)
//    {
//        if ($cursor === null) {
//            $cursor = 0;
//        }
//
//        $redis = Redis::connection('RedisApp');
//        try {
//            $arList = $redis->scan($cursor, ['count' => 100000, 'match' => '*']);
//
//            // dd($arList[1]);
//
//            $newArrayList = array_map(function ($item) {
//                $values = explode('-', $item);
//                // dd($values);
//                $values = array_pad($values, 4, "");
//
//                $headers = ['uniqueId', 'package', 'countryCode', 'ip'];
//                $rawData = array_combine($headers, $values);
//                return $rawData;
//            }, $arList[1]);
//
//            return response()->json(['message' => 'All data get successfully', 'cursor' => $arList[0], 'data' => $newArrayList]);
//
//        } catch (Exception $e) {
//            $e->getMessage();
//        }
//
//    }


    public function GetRedisData($package_name)
    {

        $redis = Redis::connection('RedisApp14');
        $arList = $redis->keys("*{$package_name}*");

        $data = $this->paginate($arList);

        return $data;

    }


        public function paginate($items, $perPage = 1000, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $result = new LengthAwarePaginator($items->forPage($page, $perPage)->values(), $items->count(), $perPage, $page, $options);
        return response()->json($result);
    }



}
