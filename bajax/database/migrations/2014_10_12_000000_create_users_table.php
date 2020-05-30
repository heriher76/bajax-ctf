<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('email')->unique();
            $table->string('password');

            $table->string('name');
            $table->string('birthplace')->nullable();
            $table->date('dateofbirth')->nullable();
            $table->text('aboutme')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();

            $table->string('point')->nullable();
            $table->timestamp('last_submit_flag')->nullable();

            $table->string('api_token')->nullable();
            $table->string('email_code')->nullable();
            $table->string('forgot_password')->nullable();
            $table->string('guard_name')->nullable();

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
        Schema::dropIfExists('users');
    }
}
