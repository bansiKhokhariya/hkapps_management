<?php

namespace App\Http\Requests;

use App\Models\AdPlacement;
use App\Models\AllApps;
use App\Models\AppDetails;
use App\Models\TestAllApp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;

class CreateTestAllAppsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'app_logo'=>'required',
            'app_name' => 'required',
            'app_packageName' => 'required|unique:all_apps,app_packageName',
//            'app_apikey'=>'nullable|unique:all_apps,app_apikey,NULL,id,deleted_at,NULL'
        ];
    }

    public function persist()
    {


        $getAllApp = TestAllApp::where('app_packageName', $this->app_packageName)->first();
        if (!$getAllApp) {

            $testAllApp = new TestAllApp($this->validated());

            //app_logo

            if ($this->hasFile('app_logo')) {
                $file = $this->file('app_logo');
                $file_name = $file->getClientOriginalName();
//                $file->move(public_path('/test_app_logo'), $file_name);
                $file_path_logo = URL::to('/') . '/app_logo/' . $file_name;
            } else {
                $file_path_logo = null;
            }

            if (!$this->hasFile('app_logo')) {
                $testAllApp->app_logo = $this->app_logo;
            } else {
                $testAllApp->app_logo = $file_path_logo;
            }

            //

            $testAllApp->app_apikey = $this->app_apikey;
            $testAllApp->save();


            // ***************** view app response json ******************** //
//            $getApp = new AllApps();
//            $result = $getApp->viewResponse($this->app_packageName,$this->app_apikey);
//
//            $redis = Redis::connection('RedisApp6');
//            $redis->set($this->app_packageName, json_encode($result));

            // ************** //

            return $testAllApp;

        }

        // call event
//        event(new RedisDataEvent());


    }
}
