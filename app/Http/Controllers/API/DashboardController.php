<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AllApps;
use App\Models\AppDetails;
use App\Models\ExpenseRevenue;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getCount()
    {
        // Apps count //
        $getAppsCount = AllApps::count();

        // spend count //
        $getTotalInvest = ExpenseRevenue::pluck('total_invest');
        $getTotalInvestMap = $getTotalInvest->map(function ($name) {
            return (float)str_replace(',', '', $name);
        });
        $getTotalInvestCount = $getTotalInvestMap->sum();

        // revenue count //
        $getRevenue = ExpenseRevenue::pluck('revenue');
        $getRevenueMap = $getRevenue->map(function ($name) {
            return (float)str_replace(',', '', $name);
        });
        $getRevenueCount = $getRevenueMap->sum();

        // download count //
        $getAllApps = AllApps::where('status', 'live')->pluck('app_packageName');
        $getAppDetailsMap = $getAllApps->map(function ($name) {
            $getAppDetails = AppDetails::where('app_packageName', $name)->first();
            if (isset($getAppDetails->realInstalls)) {
                return $getAppDetails->realInstalls;
            } else {
                return 0;
            }
        });

        $result = $getAppDetailsMap->map(function ($name) {
            return (float)str_replace(',', '', $name);
        });

        $getDownloadCount = $result->sum();
        //

        return response()->json(['Apps' => $getAppsCount, 'Spend' => $getTotalInvestCount, 'Revenue' => $getRevenueCount, 'Downloads'=>$getDownloadCount]);

    }
}
