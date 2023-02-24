<?php

namespace App\Http\Requests;

use App\Models\CommanMaster;
use Illuminate\Foundation\Http\FormRequest;

class CreateCommanMasterRequest extends FormRequest
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

    public function persist()
    {

        $commanMaster = new CommanMaster($this->validated());
        $commanMaster->save();
        return $commanMaster;

    }
}
