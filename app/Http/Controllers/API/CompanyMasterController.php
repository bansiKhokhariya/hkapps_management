<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyMasterRequest;
use App\Http\Requests\UpdateCompanyMasterRequest;
use App\Http\Resources\CompanyMasterResource;
use App\Models\CompanyMaster;
use CreateCompanyMasterTable;
use Illuminate\Http\Request;

class CompanyMasterController extends Controller
{
    public function index()
    {
        $companyMaster = CompanyMaster::all();
        return CompanyMasterResource::collection($companyMaster);
    }

    public function store(CreateCompanyMasterRequest $request)
    {
        return CompanyMasterResource::make($request->persist());
    }

    public function show(CompanyMaster $companyMaster)
    {
        return CompanyMasterResource::make($companyMaster);
    }

    public function update(UpdateCompanyMasterRequest $request, CompanyMaster $companyMaster)
    {
        return CompanyMasterResource::make($request->persist($companyMaster));
    }

    public function destroy(CompanyMaster $companyMaster)
    {
        $companyMaster->delete();
        return response('Company Master Deleted Successfully');
    }
}
