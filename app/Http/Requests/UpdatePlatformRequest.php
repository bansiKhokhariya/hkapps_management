<?php

namespace App\Http\Requests;

use App\Events\RedisDataEvent;
use App\Models\Advertise;
use App\Models\PlatForm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\URL;

class UpdatePlatformRequest extends FormRequest
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

    public function persist(PlatForm $plateform)
    {

        $plateform->fill($this->validated());

        // app_logo
        if ($this->hasFile('logo')) {
            $file = $this->file('logo');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('/logo'), $file_name);
            $file_path =  URL::to('/') . '/logo/'.$file_name;
        } else {
            $file_path = null;
        }

        if(!$this->hasFile('logo')){
            $plateform->logo = $this->logo;
        }

        if($this->logo == null){
            $plateform->logo = $plateform->logo;
        }else{
            if(($this->hasFile('logo'))){
                $plateform->logo = $file_path;
            }else{
                $plateform->logo = $this->logo;
            }
        }
        //


        $plateform->platform_name = $this->platform_name;
        $plateform->ad_format = $this->ad_format;
        if($this->status == null){
            $plateform->status = $plateform->status;
        }else{
            $plateform->status = $this->status;
        }


        $plateform->save();

        // call event
//        event(new RedisDataEvent());

        return $plateform;
    }

}
