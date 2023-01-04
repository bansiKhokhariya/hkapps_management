<?php

namespace App\Http\Requests;

use App\Models\ExpenseRevenue;
use Illuminate\Foundation\Http\FormRequest;

class CreateRevenueRequest extends FormRequest
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
            'adx'=>'required',
            'revenue'=>'required',
        ];
    }
    public function persist()
    {
        $expenseRevenue = new ExpenseRevenue($this->validated());
        $expenseRevenue->save();
        return $expenseRevenue;
    }
}