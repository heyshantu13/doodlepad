<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowerActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follower_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('follower_id')->unsigned();
        $table->bigInteger('user_id')->unsigned();
        $table->timestamps();

        $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follower_activities');
    }
}
