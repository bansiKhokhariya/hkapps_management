<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllConsoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql4')->create('all_console', function (Blueprint $table) {
            $table->increments('id');
            $table->string('manageBy')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('consoleName')->nullable();
            $table->string('status')->nullable();
            $table->string('mobile')->nullable();
            $table->string('device')->nullable();
            $table->string('remarks')->nullable();
            $table->string('blogger')->nullable();
            $table->string('privacy')->nullable();
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
        Schema::dropIfExists('all_console');
    }
}
