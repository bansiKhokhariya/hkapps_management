<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql4')->create('apps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('package_name');
            $table->string('title');
            $table->string('icon');
            $table->string('developer');
            $table->integer('company_master_id')->unsigned()->nullable();
//            $table->foreign('company_master_id')->references('id')->on('hk_apps_management.company_master');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('apps', function($table)
        {
            $table->foreign('company_master_id')->references('id')->on('hk_apps_management.company_master');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apps');
    }
}
