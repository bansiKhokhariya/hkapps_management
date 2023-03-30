<?php

namespace App\Http\Requests;

use App\Events\UserEvent;
use App\Models\AdPlacement;
use App\Models\AllApps;
use App\Models\AppDetails;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use App\Models\ApikeyList;
use App\Models\GitHubToken;

class CreateAllAppRequest extends FormRequest
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
            'app_packageName' => 'required|unique:mysql4.all_apps,app_packageName',
//            'app_apikey' => 'nullable|unique:all_apps,app_apikey,NULL,id,deleted_at,NULL'
        ];
    }

    public function persist()
    {
        $user_company = Auth::user()->company_master_id;
        $getAllApp = AllApps::where('app_packageName', $this->app_packageName)->first();
        if (!$getAllApp) {
            $id = Auth::user()->id;
            $auth_user = User::find($id);
            $allApp = new AllApps($this->validated());
            //app_logo
            if ($this->hasFile('app_logo')) {
                $file = $this->file('app_logo');
                $file_name = $file->getClientOriginalName();
                $file->move(public_path('/app_logo'), $file_name);
                $file_path_logo = URL::to('/') . '/app_logo/' . $file_name;
            } else {
                $file_path_logo = null;
            }

            if (!$this->hasFile('app_logo')) {
                $allApp->app_logo = $this->app_logo;
            } else {
                $allApp->app_logo = $file_path_logo;
            }
            $allApp->app_apikey = $this->app_apikey;
            if(Auth::user()->roles !== 'super_admin'){
                $allApp->company_master_id = $user_company;
            }
            //
            $allApp->save();

            // create github repo //
            $getToken = GitHubToken::find(1);
            $response = Http::withHeaders([
                'Authorization' =>  'Bearer '.$getToken->github_access_token,
            ])->post('https://api.github.com/user/repos', [
                'name' => $allApp->app_packageName.'_'.$allApp->id,
                'description' => 'hkApps repo',
            ]);
            // ************* //


            // delete apikey list //
            $apikeyList = ApikeyList::where('apikey_packageName', $this->app_packageName)->where('apikey_text',$this->app_apikey)->first();
            if($apikeyList){
                $apikeyList->forceDelete();
            }



//            // ***************** view app response json ******************** //
//            $getApp = new AllApps();
//            $result = $getApp->viewResponse($this->app_packageName,$this->app_apikey);
//
//            $redis = Redis::connection('RedisApp10');
//            $redis->set($this->app_packageName, json_encode($result));


            // **************** create app_details entry *************************** //

            $app_details_link = "https://lplciltdwh6kd6qjl4ytd6tzoq0iaumr.lambda-url.us-east-1.on.aws/?id=" . $allApp->app_packageName;
            $res = Http::get($app_details_link);
            if ($res->status() == 200) {
                $repo_response = $res->getBody()->getContents();
                $value = json_decode($repo_response);
                $get_app_details = AppDetails::where('allApps_id', $allApp->id)->first();
                if ($get_app_details) {
                    $appDetails = AppDetails::find($get_app_details->id);
                    $appDetails->allApps_id = $allApp->id;
                    $appDetails->description = $value->description;
                    $appDetails->descriptionHTML = $value->descriptionHTML;
                    $appDetails->summary = $value->summary;
                    $appDetails->installs = $value->installs;
                    $appDetails->minInstalls = $value->minInstalls;
                    $appDetails->realInstalls = $value->realInstalls;
                    $appDetails->score = $value->score;
                    $appDetails->ratings = $value->ratings;
                    $appDetails->reviews = $value->reviews;
                    $appDetails->histogram = $value->histogram;
                    $appDetails->price = $value->price;
                    $appDetails->free = $value->free;
                    $appDetails->currency = $value->currency;
                    $appDetails->sale = $value->sale;
                    $appDetails->saleTime = $value->saleTime;
                    $appDetails->originalPrice = $value->originalPrice;
                    $appDetails->saleText = $value->saleText;
                    $appDetails->offersIAP = $value->offersIAP;
                    $appDetails->inAppProductPrice = $value->inAppProductPrice;
                    $appDetails->developer = $value->developer;
                    $appDetails->developerId = $value->developerId;
                    $appDetails->developerEmail = $value->developerEmail;
                    $appDetails->developerWebsite = $value->developerWebsite;
                    $appDetails->developerAddress = $value->developerAddress;
                    $appDetails->genre = $value->genre;
                    $appDetails->genreId = $value->genreId;
                    $appDetails->headerImage = $value->headerImage;
                    $appDetails->screenshots = $value->screenshots;
                    $appDetails->video = $value->video;
                    $appDetails->videoImage = $value->videoImage;
                    $appDetails->contentRating = $value->contentRating;
                    $appDetails->contentRatingDescription = $value->contentRatingDescription;
                    $appDetails->adSupported = $value->adSupported;
                    $appDetails->containsAds = $value->containsAds;
                    $appDetails->released = $value->released;
                    $appDetails->updated = $value->updated;
                    $appDetails->version = $value->version;
                    $appDetails->recentChanges = $value->recentChanges;
                    $appDetails->recentChangesHTML = $value->recentChangesHTML;
                    $appDetails->comments = $value->comments;
                    $appDetails->url = $value->url;
                    $appDetails->save();

                } else {
                    $appDetails = new AppDetails();
                    $appDetails->allApps_id = $allApp->id;
                    $appDetails->description = $value->description;
                    $appDetails->descriptionHTML = $value->descriptionHTML;
                    $appDetails->summary = $value->summary;
                    $appDetails->installs = $value->installs;
                    $appDetails->minInstalls = $value->minInstalls;
                    $appDetails->realInstalls = $value->realInstalls;
                    $appDetails->score = $value->score;
                    $appDetails->ratings = $value->ratings;
                    $appDetails->reviews = $value->reviews;
                    $appDetails->histogram = json_encode($value->histogram);
                    $appDetails->price = $value->price;
                    $appDetails->free = $value->free;
                    $appDetails->currency = $value->currency;
                    $appDetails->sale = $value->sale;
                    $appDetails->saleTime = $value->saleTime;
                    $appDetails->originalPrice = $value->originalPrice;
                    $appDetails->saleText = $value->saleText;
                    $appDetails->offersIAP = $value->offersIAP;
                    $appDetails->inAppProductPrice = $value->inAppProductPrice;
                    $appDetails->developer = $value->developer;
                    $appDetails->developerId = $value->developerId;
                    $appDetails->developerEmail = $value->developerEmail;
                    $appDetails->developerWebsite = $value->developerWebsite;
                    $appDetails->developerAddress = $value->developerAddress;
                    $appDetails->genre = $value->genre;
                    $appDetails->genreId = $value->genreId;
                    $appDetails->headerImage = $value->headerImage;
                    $appDetails->screenshots = json_encode($value->screenshots);
                    $appDetails->video = $value->video;
                    $appDetails->videoImage = $value->videoImage;
                    $appDetails->contentRating = $value->contentRating;
                    $appDetails->contentRatingDescription = $value->contentRatingDescription;
                    $appDetails->adSupported = $value->adSupported;
                    $appDetails->containsAds = $value->containsAds;
                    $appDetails->released = $value->released;
                    $appDetails->updated = $value->updated;
                    $appDetails->version = $value->version;
                    $appDetails->recentChanges = $value->recentChanges;
                    $appDetails->recentChangesHTML = $value->recentChangesHTML;
                    $appDetails->comments = json_encode($value->comments);
                    $appDetails->url = $value->url;
                    $appDetails->status = 'live';
                    $appDetails->save();

                }

            }

            // **************** //

            // call event
            // event(new UserEvent($auth_user));

            return $allApp;

        }

    }
}
