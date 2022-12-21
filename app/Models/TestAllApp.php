<?php

namespace App\Models;

use App\Http\Resources\TestAdPlacementResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TestAdPlacement;
use Illuminate\Database\Eloquent\Model;

class TestAllApp extends Model
{
    use HasFactory;
    protected $table = 'test_all_apps';
    protected $guarded=[];

    public function TestAdPlacement()
    {

        $adplacement = TestAdPlacement::where('allApps_id', $this->id)->get();
        return TestAdPlacementResource::collection($adplacement);

    }
}
