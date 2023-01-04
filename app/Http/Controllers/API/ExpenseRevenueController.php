<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\CreateExpenseRevenueRequest;
use App\Http\Requests\CreateRevenueRequest;
use App\Http\Requests\UpdateExpenseRevenueRequest;
use App\Http\Resources\ExepenseRevenueResource;
use App\Models\ExpenseRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseRevenueController extends Controller
{
    public function index()
    {
//        $result = DB::table("expense_revenue as exp")
//            ->select("exp.id","exp.package_name",
//                DB::raw("(select ads_master from expense_revenue where expense_revenue.package_name = exp.package_name and ads_master is not null limit 1) as ads_master"),
//                DB::raw("sum(exp.total_invest) as total_invest"),
//                DB::raw("(select adx from expense_revenue where expense_revenue.package_name = exp.package_name and adx is not null limit 1) as adx"),
//                DB::raw("sum(exp.revenue) as revenue"),
//                DB::raw("exp.created_at as created_at"))
//            ->groupBy("package_name")
//            ->get();
        $result = ExpenseRevenue::select("id", "package_name",
            DB::raw("(select ads_master from expense_revenue where expense_revenue.package_name = package_name and ads_master is not null limit 1) as ads_master"),
            DB::raw("sum(total_invest) as total_invest"),
            DB::raw("(select adx from expense_revenue where expense_revenue.package_name = package_name and adx is not null limit 1) as adx"),
            DB::raw("sum(revenue) as revenue"),
            DB::raw("created_at as created_at"))
            ->groupBy("package_name")
            ->get();

        return ExepenseRevenueResource::collection($result);
    }

    public function store(CreateExpenseRevenueRequest $request)
    {
        return ExepenseRevenueResource::make($request->persist());
    }

    public function show(ExpenseRevenue $expenseRevenue)
    {
        return ExepenseRevenueResource::make($expenseRevenue);
    }

    public function update(UpdateExpenseRevenueRequest $request, ExpenseRevenue $expenseRevenue)
    {
        return ExepenseRevenueResource::make($request->persist($expenseRevenue));
    }

    public function destroy(ExpenseRevenue $expenseRevenue)
    {
        $expenseRevenue->delete();
        return response('Expense Revenue Deleted Successfully');
    }

    public function storeExpense(CreateExpenseRequest $request)
    {
        return ExepenseRevenueResource::make($request->persist());
    }

    public function storeRevenue(CreateRevenueRequest $request)
    {

        return ExepenseRevenueResource::make($request->persist());
    }
}
