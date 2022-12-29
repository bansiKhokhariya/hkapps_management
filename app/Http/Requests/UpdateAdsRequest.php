<?php

namespace App\Http\Requests;

use App\Models\AdsMaster;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdsRequest extends FormRequest
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
            'tel_id'=>'required',
            'cid'=>'required',
            'ads_master'=>'required',
        ];
    }
    public function persist(AdsMaster $AdsMaster)
    {
        $AdsMaster->fill($this->validated());
        $AdsMaster->save();
        return $AdsMaster;
    }
}
