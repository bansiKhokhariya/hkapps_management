<?php

namespace App\Http\Requests;

use App\Models\AdsMaster;
use App\Models\AllApps;
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
        if(Auth::user()->roles !== 'super_admin'){
            $expenseRevenue->company_master_id = $user_company;
        }
        $expenseRevenue->save();

        $getAdsMaster = AdsMaster::where('id',$this->ads_master)->first();
        $allApps = AllApps::where('app_packageName',$this->package_name)->first();
        if ($allApps->ads_master) {
            $meta_keywords = explode(',', $allApps->ads_master);
            if(!in_array($getAdsMaster->ads_master, $meta_keywords)){
                $allApps->ads_master =  $allApps->ads_master . ',' . $getAdsMaster->ads_master;
            }
        } else {
            $allApps->ads_master = $getAdsMaster->ads_master;
        }
        $allApps->save();

        return $expenseRevenue;
    }
}
