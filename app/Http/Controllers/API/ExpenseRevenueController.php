<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExpenseRevenueRequest;
use App\Http\Requests\UpdateExpenseRevenueRequest;
use App\Http\Resources\ExepenseRevenueResource;
use App\Models\ExpenseRevenue;
use Illuminate\Http\Request;

class ExpenseRevenueController extends Controller
{
    public function index()
    {
        $expenseRevenue = ExpenseRevenue::get();
        return ExepenseRevenueResource::collection($expenseRevenue);
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
}
