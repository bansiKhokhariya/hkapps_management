<?php

namespace App\Http\Requests;

use App\Models\PartyMaster;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePartyRequest extends FormRequest
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
    public function persist(PartyMaster $partyMaster)
    {
        $partyMaster->fill($this->validated());
        $partyMaster->save();
        return $partyMaster;
    }
}
