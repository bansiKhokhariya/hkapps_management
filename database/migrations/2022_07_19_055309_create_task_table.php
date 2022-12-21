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
            $table->increments('id');
            $table->string('app_no')->nullable();
            $table->string('title')->nullable();
            $table->string('package_name')->nullable();
            $table->string('reference_app')->nullable();
            $table->string('repo_link')->nullable();
            $table->text('attchments')->nullable();
            $table->text('attchments_link')->nullable();
            $table->string('console_app')->nullable();
            $table->string('assigned_people')->nullable();
            $table->integer('assign_person')->unsigned()->nullable();
            $table->enum('phase',[
                'designing' => \App\Models\Task::designing,
                'developing'  => \App\Models\Task::developing,
                'testing_designing' => \App\Models\Task::testing_designing,
                'testing_developing' => \App\Models\Task::testing_developing,
                'production' => \App\Models\Task::production,
            ])->nullable();
            $table->string('prev_assign_person')->nullable();
            $table->text('description')->nullable();
            $table->enum('status',[
                'pending'  => \App\Models\Task::pending,
                'working' => \App\Models\Task::working,
                'ready_testing' => \App\Models\Task::ready_testing,
                'done' => \App\Models\Task::done,
                're-working' => \App\Models\Task::re_working,
                ])->nullable();
            $table->enum('priority',[
                'low'  => \App\Models\Task::low,
                'medium' => \App\Models\Task::medium,
                'high' => \App\Models\Task::high,
            ])->nullable();
            $table->integer('assign_aso')->unsigned()->nullable();
            $table->enum('aso_status',[
                'pending'  => \App\Models\Task::pending,
                'working' => \App\Models\Task::working,
                'done' => \App\Models\Task::done,
            ])->nullable();
            $table->string('deadline')->nullable();
            $table->string('assigned_date')->nullable();
            $table->string('completed_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('task', function($table)
        {
            $table->foreign('assign_person')->references('id')->on('users');
            $table->foreign('assign_aso')->references('id')->on('users');
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
