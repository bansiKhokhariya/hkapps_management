<?php

namespace App\Http\Requests;

use App\Models\ExpenseRevenue;
use Illuminate\Foundation\Http\FormRequest;

class CreateExpenseRevenueRequest extends FormRequest
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
        $expenseRevenue = new ExpenseRevenue($this->validated());
        $expenseRevenue->package_name = $this->package_name;
        $expenseRevenue->ads_master = $this->ads_master;
        $expenseRevenue->total_invest = $this->total_invest;
        $expenseRevenue->adx = $this->adx;
        $expenseRevenue->revenue = $this->revenue;
        $expenseRevenue->save();
        return $expenseRevenue;
    }
}
