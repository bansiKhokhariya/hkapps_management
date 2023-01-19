<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('designation',[
                'Super Admin'=>\App\Models\User::superadmin,
                'admin'=>\App\Models\User::admin,
                'designer' => \App\Models\User::designer,
                'developer'  => \App\Models\User::developer,
                'tester' => \App\Models\User::tester,
                'ASO' => \App\Models\User::aso,
                'production'=>\App\Models\User::production,

            ])->nullable();
            $table->string('roles');
            $table->string('profile_image')->nullable();
            $table->integer('company_master_id')->unsigned()->nullable();
            $table->foreign('company_master_id')->references('id')->on('company_master');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
//        Schema::table('users', function($table)
//        {
//            $table->foreign('company_master_id')->references('id')->on('company_master');
//        });

    }

     /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
