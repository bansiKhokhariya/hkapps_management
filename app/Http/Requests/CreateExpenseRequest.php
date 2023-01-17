<?php

namespace App\Http\Requests;

use App\Models\ExpenseRevenue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateExpenseRequest extends FormRequest
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
            'package_name'=>'required',
            'ads_master'=>'required',
            'total_invest'=>'required',
        ];
    }
    public function persist()
    {
        $user_company = Auth::user()->company_master_id;
        $expenseRevenue = new ExpenseRevenue($this->validated());
        if(Auth::user()->role !== 'super_admin'){
            $expenseRevenue->company_master_id = $user_company;
        }
        $expenseRevenue->save();
        return $expenseRevenue;
    }
}
