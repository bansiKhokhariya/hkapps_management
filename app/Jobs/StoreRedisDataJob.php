<?php

namespace App\Jobs;

use App\Events\RedisDataEvent;
use App\Models\AllAppsHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class StoreRedisDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        \Log::info('in the start');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle($cursor = null)
    {
        \Log::info('in the queue');
        if ($cursor === null) {
            $cursor = 0;
        }
        $redis = Redis::connection('RedisApp14');
        do {
            \Log::info($cursor);
            $arList = $redis->scan($cursor, ['count' => 100000, 'match' => '*']);
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
            $cursor = $arList[0];
            sleep(5);
        }
        while ($cursor !== 0);
        \Log::info('queue run succesfully!');
//        return response()->json(['message' => 'data added successfully', 'data' => $arList[0]]);
    }
}
