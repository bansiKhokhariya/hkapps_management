<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdxMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adx_master', function (Blueprint $table) {
            $table->id();
            $table->string('adx_register_company');
            $table->string('adx');
            $table->integer('adx_share');
            $table->string('type');
            $table->integer('company_master_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('adx_master', function ($table) {
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
        Schema::dropIfExists('adx_master');
    }
}
