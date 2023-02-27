<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpyAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql4')->create('spy_apps', function (Blueprint $table) {
            $table->id();
            $table->string('packageName')->nullable();
            $table->string('url')->nullable();
            $table->string('locale')->nullable();
            $table->string('country')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('developerName')->nullable();
            $table->string('icon')->nullable();
            $table->string('screenshots')->nullable();
            $table->string('score')->nullable();
            $table->string('priceText')->nullable();
            $table->string('installsText')->nullable();
            $table->string('released')->nullable();
            $table->string('updated')->nullable();
            $table->string('version')->nullable();
            $table->string('category')->nullable();
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
        Schema::dropIfExists('spy_apps');
    }
}
