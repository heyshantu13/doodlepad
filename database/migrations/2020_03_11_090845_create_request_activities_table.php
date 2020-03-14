<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
        $table->integer('follower_id')->unsigned();
        $table->integer('user_id')->unsigned();
        $table->text('media_url')->nullable();
          $table->enum('type', config('constants.enums.request_activities'));

            $table->softDeletes();

        $table->timestamps();

        $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_activities');
    }
}
