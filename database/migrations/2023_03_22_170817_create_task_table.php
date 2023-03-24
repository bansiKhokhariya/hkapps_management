<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('refrence')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->string('developerStatus')->default('pending');
            $table->string('developerStartDate')->nullable();
            $table->string('developerEndDate')->nullable();
            $table->string('assignDeveloperName')->nullable();
            $table->string('designerStatus')->default('pending');
            $table->string('designerEndDate')->nullable();
            $table->string('designerStartDate')->nullable();
            $table->string('assignDesignerName')->nullable();
            $table->string('githubRepoLink')->nullable();
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
        Schema::dropIfExists('task');
    }
}
