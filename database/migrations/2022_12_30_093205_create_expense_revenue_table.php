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
            $table->increments('id');
            $table->string('package_name')->nullable();
            $table->integer('ads_master')->unsigned()->nullable();
//            $table->unsignedBigInteger('ads_master');
            $table->string('total_invest')->nullable();
            $table->integer('adx')->unsigned()->nullable();
//            $table->unsignedBigInteger('adx');
            $table->string('revenue')->nullable();
//            $table->foreign('ads_master')->references('id')->on('ads_master')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('adx')->references('id')->on('adx_master')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('company_master_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('expense_revenue', function ($table) {
            $table->foreign('ads_master')->references('id')->on('ads_master');
            $table->foreign('adx')->references('id')->on('adx_master');
            $table->foreign('company_master_id')->references('id')->on('company_master');

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
