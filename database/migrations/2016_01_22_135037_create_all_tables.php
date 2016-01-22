<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      // Create users table
      Schema::create('st_users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('username', 80)->unique();
        $table->string('password', 64);
        $table->string('user_type', 75);
        $table->string('email', 80)->unique();
        $table->boolean('show_weekends');
        $table->dateTime('day_start_time');
        $table->dateTime('day_end_time');
        $table->dateTime('dt');
        $table->integer('company_id');
        $table->integer('office_no');
        $table->string('office_name');
        $table->integer('office_size');
        $table->string('zip', 10);
        $table->dateTime('first_login');
        $table->dateTime('last_login');
        $table->integer('login_count');
        $table->dateTime('expires');
        $table->string('resetToken', 64);
      });

      // Create category table
      Schema::create('st_category', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('company_id'); // FK
        $table->string('title', 80);
        $table->string('abbreviation', 80);
        $table->text('description');
        $table->string('color', 6); // hex code
      });

      // Create sub_category table
      Schema::create('st_sub_category', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('category_id');
        $table->string('title', 80);
        $table->string('abbreviation', 80);
        $table->text('description');

        $table->foreign('category_id')->references('id')->on('st_category');
      });

      // Create client table
      Schema::create('st_client', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id');
        $table->integer('client_code');
        $table->string('name', 80);
        $table->boolean('gender');
        $table->string('type', 10);

        $table->foreign('user_id')->references('id')->on('st_users');
      });

      // Create calendar table
      Schema::create('st_calendar', function (Blueprint $table) {
        $table->increments('id');
        $table->increments('user_id');
        $table->integer('category_id');
        $table->integer('sub_category_id');
        $table->integer('sub_sub_category_id');
        $table->integer('client_id');
        $table->string('title', 80);
        $table->text('description');
        $table->boolean('all_day');
        $table->dateTime('start');
        $table->dateTime('end');
        $table->integer('duration');
        $table->string('color', 6);

        $table->foreign('user_id')->references('id')->on('st_users');
        $table->foreign('category_id')->references('id')->on('st_category');
        $table->foreign('sub_category_id')->references('id')->on('st_category');
        $table->foreign('sub_sub_category_id')->references('id')->on('st_category');
        $table->foreign('client_id')->references('id')->on('st_client');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      // Drop users table
      Schema::drop('st_users');
      // Drop category table
      Schema::drop('st_category');
      // Drop sub_category table
      Schema::drop('st_sub_category');
      // Drop client table
      Schema::drop('st_client');
      // Drop users table
      Schema::drop('st_calendar');
    }
}
