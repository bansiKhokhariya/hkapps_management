<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ExpenseRevenue extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'expense_revenue';
    protected $guarded = [];
    protected static $logName = 'Expense Revenue';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} ExpenseRevenue - ID:{$this->id}, PackageName:{$this->package_name}";
    }

    public function masterAds()
    {
        return $this->belongsTo(AdsMaster::class, 'ads_master', 'id');
    }

    public function Adx_master()
    {
        return $this->belongsTo(AdxMaster::class, 'adx', 'id');
    }

    public function icon()
    {
        $get_app = AllApps::where('app_packageName', $this->package_name)->pluck('app_logo')->first();
        if ($get_app) {
            return $get_app;
        } else {
            return null;
        }

    }

}
