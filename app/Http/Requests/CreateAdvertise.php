<?php

namespace App\Http\Requests;

use App\Events\RedisDataEvent;
use App\Models\Advertise;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\URL;

class CreateAdvertise extends FormRequest
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
            'app_name'=>'required',
            'app_packageName'=>'required',
            'app_logo'=>'required',
            'app_banner'=>'required',
            'app_shortDecription'=>'required',
            'app_buttonName'=>'required',
            'app_rating'=>'required',
            'app_download'=>'required',
            'app_AdFormat'=>'required',
        ];
    }
    public function persist()
    {
        $advertise = new Advertise($this->validated());

        //app_logo
        if ($this->hasFile('app_logo')) {
            $file = $this->file('app_logo');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('/app_logo'), $file_name);
            $file_path_logo =  URL::to('/') . '/app_logo/'.$file_name;
        } else {
            $file_path_logo = null;
        }

        if(!$this->hasFile('app_logo')){
            $advertise->app_logo = $this->app_logo;
        }else{
            $advertise->app_logo = $file_path_logo;
        }
        //

        //app_banner
        if ($this->hasFile('app_banner')) {
            $file = $this->file('app_banner');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('/app_banner'), $file_name);
            $file_path_banner =  URL::to('/') . '/app_banner/'.$file_name;
        } else {
            $file_path_banner = null;
        }

        if(!$this->hasFile('app_banner')){
            $advertise->app_banner = $this->app_banner;
        }else{
            $advertise->app_banner = $file_path_banner;
        }
        //


        $advertise->app_AdFormat = $this->app_AdFormat;

        $advertise->save();

        // call event
        event(new RedisDataEvent());

        return $advertise;
    }
}
