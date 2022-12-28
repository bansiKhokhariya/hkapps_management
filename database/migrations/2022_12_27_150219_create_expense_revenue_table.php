<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseRevenueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_revenue', function (Blueprint $table) {
            $table->id();
            $table->string('package_name')->nullable();
            $table->string('ads_master')->nullable();
            $table->string('total_invest')->nullable();
            $table->string('adx')->nullable();
            $table->string('revenue')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_revenue');
    }
}
