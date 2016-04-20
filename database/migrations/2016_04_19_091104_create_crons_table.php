<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('action')->unique();
            $table->mediumText('data')->nullable();
            $table->dateTime('next')->nullable();
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->unsignedInteger('interval');
            $table->boolean('active');
            $table->string('informemail')->nullable();
            $table->unsignedInteger('plugin_id')->nullable();
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
        Schema::drop('crons');
    }
}
