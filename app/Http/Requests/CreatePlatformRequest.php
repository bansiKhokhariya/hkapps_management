<?php

namespace App\Http\Requests;

use App\Events\RedisDataEvent;
use App\Models\PlatForm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class CreatePlatformRequest extends FormRequest
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
            'logo'=>'required',
            'platform_name'=>'required',
            'ad_format'=>'required'
        ];
    }

    public function persist()
    {
        $user_company = Auth::user()->company_master_id;
        $platform = new PlatForm($this->validated());

        //logo
        if ($this->hasFile('logo')) {
            $file = $this->file('logo');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('/logo'), $file_name);
            $file_path_logo =  URL::to('/') . '/logo/'.$file_name;
        } else {
            $file_path_logo = null;
        }

        if(!$this->hasFile('logo')){
            $platform->logo = $this->logo;
        }else{
            $platform->logo = $file_path_logo;
        }
        //


        $platform->platform_name = $this->platform_name;
        $platform->ad_format = $this->ad_format;
        $platform->company_master_id = $user_company;

        $platform->save();

        // call event
        // event(new RedisDataEvent());

        return $platform;
    }

}
