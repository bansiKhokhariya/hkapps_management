<?php

namespace App\Http\Controllers\API;

use App\Events\RedisDataEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityHistoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;


class ActivityHistoryController extends Controller
{
    public function index(){
        $activity = Activity::latest()->get();
        return ActivityHistoryResource::collection($activity);
    }

    public function filterDateRangeOrUser(Request $request)
    {

        $user_id = $request->user_id;
        $time = $request->time;
        $carbonNow = Carbon::now()->format('Y-m-d');
        $carbonYesterday = Carbon::yesterday()->format('Y-m-d');
        $carbon7Day = Carbon::now()->subDays(7);
        $carbon30Day = Carbon::now()->subDays(30);
        $carbonThisMonth = Carbon::now()->month;
        $carbonLastMonth = Carbon::now()->subMonth()->month;


        if ($user_id) {

            $activity = Activity::where('causer_id', $user_id)->get();
            return ActivityHistoryResource::collection($activity);

        } elseif ($time && !$user_id) {

            if ($time == 'today') {

                $activity = Activity::whereDate('created_at', $carbonNow)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time == 'yesterday') {

                $activity = Activity::whereDate('created_at', $carbonYesterday)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time == 'last7days') {

                $activity = Activity::where('created_at', '>=', $carbon7Day)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time == 'last30days') {

                $activity = Activity::where('created_at', '>=', $carbon30Day)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time == 'thismonth') {

                $activity = Activity::whereMonth('created_at', '>=', $carbonThisMonth)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time == 'lastmonth') {

                $activity = Activity::whereMonth(
                    'created_at', '=', $carbonLastMonth
                )->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time=='customrange') {

                $start_date = \Carbon\Carbon::createFromFormat('Y-m-d', $request->start_date);
                $end_date = \Carbon\Carbon::createFromFormat('Y-m-d', $request->end_date);

                $activity = Activity::whereBetween('created_at', [$start_date, $end_date])->get();
                return ActivityHistoryResource::collection($activity);

            }

        } elseif ($time && $user_id) {

            if ($time == 'today') {

                $activity = Activity::whereDate('created_at', $carbonNow)->where('causer_id',$user_id)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time == 'yesterday') {

                $activity = Activity::whereDate('created_at', $carbonYesterday)->where('causer_id',$user_id)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time == 'last7days') {

                $activity = Activity::where('created_at', '>=', $carbon7Day)->where('causer_id',$user_id)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time == 'last30days') {

                $activity = Activity::where('created_at', '>=', $carbon30Day)->where('causer_id',$user_id)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time == 'thismonth') {

                $activity = Activity::whereMonth('created_at', '>=', $carbonThisMonth)->where('causer_id',$user_id)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time == 'lastmonth') {

                $activity = Activity::whereMonth(
                    'created_at', '=', $carbonLastMonth
                )->where('causer_id',$user_id)->get();
                return ActivityHistoryResource::collection($activity);

            } elseif ($time=='customrange') {


                $start_date = \Carbon\Carbon::createFromFormat('Y-m-d', $request->start_date);
                $end_date = \Carbon\Carbon::createFromFormat('Y-m-d', $request->end_date);

                $activity = Activity::whereBetween('created_at', [$start_date, $end_date])->where('causer_id',$user_id)->get();
                return ActivityHistoryResource::collection($activity);

            }

        } else {
            $activity = Activity::all();
            return ActivityHistoryResource::collection($activity);
        }

    }

    public function activityStore(Request $request){

        $activity = new Activity();
        $activity->log_name = $request->log_name;
        $activity->description = $request->description;
        $activity->causer_id =  $request->causer_id;
        $activity->save();

        // call event
         event(new RedisDataEvent());

        return $activity;

    }



}
