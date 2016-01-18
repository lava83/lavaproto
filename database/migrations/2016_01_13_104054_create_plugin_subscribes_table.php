<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePluginSubscribesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plugin_subscribes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('plugin_id');
            $table->string('subscribe');
            $table->string('listener');
            $table->integer('position')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('plugin_id')
                ->references('id')
                ->on('plugins')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('plugin_subscribes');
    }
}
