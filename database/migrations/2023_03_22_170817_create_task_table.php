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
            $table->string('designerStartDate')->nullable();
            $table->string('designerEndDate')->nullable();
            $table->string('assignDesignerName')->nullable();
            $table->string('testerStatus')->default('pending');
            $table->string('testerStartDate')->nullable();
            $table->string('testerEndDate')->nullable();
            $table->string('assignTesterName')->nullable();
            $table->string('dev_testing')->default('false');
            $table->string('des_testing')->default('false');
            $table->string('githubRepoLink')->nullable();
            $table->string('figmaLink')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->text('screenshots')->nullable();
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
