<?php

namespace App\Http\Requests;

use App\Events\RedisDataEvent;
use App\Models\ApikeyList;
use Illuminate\Foundation\Http\FormRequest;

class CreateApiKeyListRequest extends FormRequest
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
            'apikey_text'=>'required',
            'apikey_packageName'=>'required',
            'apikey_appID'=>'required',
            'apikey_request'=>'required',
        ];
    }

    public function persist()
    {
        $apikey_list = new ApikeyList($this->validated());
        $apikey_list->save();
        // call event
        // event(new RedisDataEvent());

        return $apikey_list;
    }
}
