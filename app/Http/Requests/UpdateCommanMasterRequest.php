<?php

namespace App\Http\Requests;

use App\Models\CommanMaster;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommanMasterRequest extends FormRequest
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
            'vpn_id' => 'required',
            'value' => 'required',
            'type' => 'required'
        ];
    }
    public function persist(CommanMaster $commanMaster)
    {
        $commanMaster->fill($this->validated());
        $commanMaster->save();
        return $commanMaster;
    }
}
