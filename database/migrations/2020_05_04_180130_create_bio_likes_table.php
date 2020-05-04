<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bio_likes', function (Blueprint $table) {
                $table->bigIncrements('id');
        $table->bigInteger('profile_id')->unsigned();
        $table->bigInteger('user_id')->unsigned();
        $table->foreign('profile_id')->references('id')->on('user_profiles')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('bio_likes');
    }
}
