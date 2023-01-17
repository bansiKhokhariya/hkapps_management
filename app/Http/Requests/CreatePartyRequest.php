<?php

namespace App\Http\Requests;

use App\Models\PartyMaster;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreatePartyRequest extends FormRequest
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
            'party'=>'required',
            'company_name'=>'required',
        ];
    }
    public function persist()
    {
        $user_company = Auth::user()->company_master_id;
        $PartyMaster = new PartyMaster($this->validated());
        $PartyMaster->company_master_id = $user_company;
        $PartyMaster->save();
        return $PartyMaster;

    }
}
