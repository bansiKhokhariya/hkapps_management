<?php

namespace App\Http\Requests;

use App\Models\AdxMaster;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateAdxRequest extends FormRequest
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
            'adx_register_company' => 'required',
            'adx' => 'required',
            'adx_share' => 'required',
            'type' => 'required'
        ];
    }

    public function persist()
    {
        $user_company = Auth::user()->company_master_id;
        $AdxMaster = new AdxMaster($this->validated());
        $AdxMaster->company_master_id = $user_company;
        $AdxMaster->save();
        return $AdxMaster;

    }
}
