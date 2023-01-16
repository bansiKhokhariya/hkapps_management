<?php

namespace App\Http\Requests;

use App\Models\CompanyMaster;
use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyMasterRequest extends FormRequest
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
            'company'=>'required',
            'person_name'=>'required',
        ];
    }
    public function persist()
    {

        $companyMaster = new CompanyMaster($this->validated());
        $companyMaster->save();
        return $companyMaster;

    }
}
