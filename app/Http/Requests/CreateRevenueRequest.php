<?php

namespace App\Http\Requests;

use App\Models\AdsMaster;
use App\Models\AdxMaster;
use App\Models\AllApps;
use App\Models\ExpenseRevenue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
        $user_company = Auth::user()->company_master_id;
        $expenseRevenue = new ExpenseRevenue($this->validated());
        if(Auth::user()->roles !== 'super_admin'){
            $expenseRevenue->company_master_id = $user_company;
        }
        $expenseRevenue->save();

        $getAdxMaster = AdxMaster::where('id',$this->adx)->first();
        $allApps = AllApps::where('app_packageName',$this->package_name)->first();
        if ($allApps->adx) {
            $meta_keywords = explode(',', $allApps->adx);
            if(!in_array($getAdxMaster->adx, $meta_keywords)){
                $allApps->adx =  $allApps->adx . ',' . $getAdxMaster->adx;
            }
        } else {
            $allApps->adx = $getAdxMaster->adx;
        }
        $allApps->save();

        return $expenseRevenue;
    }
}
