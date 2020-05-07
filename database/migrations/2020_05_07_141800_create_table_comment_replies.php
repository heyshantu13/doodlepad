<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCommentReplies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_replies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('comment_id')->unsigned();
            $table->bigInteger('user_profile_id')->unsigned();
            $table->string('text')->nullable();
            $table->string('type')->default('TEXT');
             $table->string('media_url')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_profile_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_replies');
    }
}
