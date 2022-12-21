<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApikeyListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apikey_list', function (Blueprint $table) {
            $table->id();
            $table->text('apikey_text')->nullable();
            $table->string('apikey_packageName')->nullable();
            $table->integer('apikey_appID')->nullable();
            $table->integer('apikey_request')->default(1);
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
        Schema::dropIfExists('apikey_list');
    }
}
