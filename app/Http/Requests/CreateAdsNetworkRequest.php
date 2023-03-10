<?php

namespace App\Http\Requests;

use App\Models\AdsNetwork;
use Illuminate\Foundation\Http\FormRequest;

class CreateAdsNetworkRequest extends FormRequest
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

        ];
    }

    public function persist()
    {
        $adsNetwork = new AdsNetwork($this->validated());
        $adsNetwork->seller = $this->seller;
        $adsNetwork->networkCode = $this->networkCode;
        $adsNetwork->type = $this->type;
        $adsNetwork->uniqueCode = $this->uniqueCode;
        $adsNetwork->save();
        return $adsNetwork;

    }
}
