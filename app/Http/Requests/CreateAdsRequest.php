<?php

namespace App\Http\Requests;

use App\Models\AdsMaster;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateAdsRequest extends FormRequest
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
    public function persist()
    {
        $user_company = Auth::user()->company_master_id;
        $AdsMaster = new AdsMaster($this->validated());
        if(Auth::user()->role !== 'super_admin'){
            $AdsMaster->company_master_id = $user_company;
        }

        $AdsMaster->save();
        return $AdsMaster;

    }
}
