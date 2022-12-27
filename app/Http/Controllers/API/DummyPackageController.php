<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DummyPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DummyPackageController extends Controller
{
    public function index($status_code = null)
    {
        if ($status_code) {
            $data = DummyPackage::where('status_code', $status_code)->get();
        } else {
            $data = DummyPackage::all();
        }
        return $data;
    }
    // public function store(Request $request){

    //       $get_package = DummyPackage::where('package_name',$request->package_name)->get();
    //     if(!$get_package){
    //         $dummy_package = new DummyPackage();
    //         $dummy_package->package_name = $request->package_name;
    //         $dummy_package->status_code = $request->status_code;
    //         $dummy_package->save();
    //         return $dummy_package;
    //     }else{
    //         $dummy_package = DummyPackage::find($get_package[0]->id);
    //         $dummy_package->package_name = $request->package_name;
    //         $dummy_package->status_code = $request->status_code;
    //         $dummy_package->save();
    //         return $dummy_package;
    //     }
    // }
    public function store($package_name){

        $app_link = "https://play.google.com/store/apps/details?id=".$package_name;

        $res = Http::get($app_link);
        // dd($res->status()==200);
        if($res->status()!==200) {
            // dd('save');

            $get_package = DummyPackage::where('package_name',$package_name)->first();
            // dd($get_package);
            if(!$get_package){
                $dummy_package = new DummyPackage();
                $dummy_package->package_name = $package_name;
                $dummy_package->status_code = $res->status();
                $dummy_package->save();
                return response()->json($dummy_package);
            }else{
                $dummy_package = DummyPackage::find($get_package->id);
                $dummy_package->package_name = $package_name;
                $dummy_package->status_code = $res->status();
                $dummy_package->save();
                return response()->json($dummy_package);
            }

        }
        else {
            // dd('dont save');
            return response()->json(['status'=>$res->status()]);
        }
    }

}
