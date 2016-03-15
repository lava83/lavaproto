<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePluginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plugins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('namespace');
            $table->string('source');
            $table->string('name');
            $table->unsignedInteger('version')->nullable()->default(1);
            $table->string('author')->nullable();
            $table->string('copyright')->nullable();
            $table->string('license')->nullable();
            $table->string('description')->nullable();
            $table->string('link')->nullable();
            $table->string('support_link')->nullable();
            $table->boolean('installed')->default(false);
            $table->boolean('active')->default(false);
            $table->dateTime('installation_date')->nullable();
            $table->dateTime('activation_date')->nullable();
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
        Schema::drop('plugins');
    }
}
