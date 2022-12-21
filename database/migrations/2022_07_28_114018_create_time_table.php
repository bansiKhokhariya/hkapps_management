<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('start_date')->nullable();
            $table->integer('time')->nullable();
            $table->string('stop_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('is_started')->nullable();
            $table->string('assigned_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('time',function($table)
        {
            $table->foreign('task_id')->references('id')->on('task')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time');
    }
}
