<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpyAppDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql4')->create('spy_app_details', function (Blueprint $table) {
            $table->id();
            $table->string('packageName')->nullable();
            $table->string('daily_installs')->nullable();
            $table->string('downloads')->nullable();
            $table->string('ratings')->nullable();
            $table->string('reviews')->nullable();
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
        Schema::dropIfExists('spy_app_details');
    }
}
