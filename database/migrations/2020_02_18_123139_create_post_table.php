<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            // $table->unsignedBigInteger('id');
            // $table->integer('user_profile_id')->unsigned();
            // $table->enum('type', config('constants.enums.post_type'));
            // $table->string('text')->nullable();
            // $table->enum('alignment',['left','center','right'])->nullable();
            // $table->enum('color',['0','1']);
            // $table->string('caption')->nullable();
            // $table->decimal('latitude', 10, 8)->nullable();
            // $table->decimal('longitude', 11, 8)->nullable();
            // $table->string('path',500)->nullable();
            // $table->string('thumbnail_url')->nullable();
            // $table->string('text_location')->nullable();
            // $table->boolean('is_pinned')->default(false);
            // $table->softDeletes();
            // $table->timestamps();
            // $table->foreign('user_profile_id')
            // ->references('id')
            // ->on('user_profiles')
            // ->onDelete('cascade');

             $table->bigIncrements('id');
            $table->bigInteger('user_profile_id')->unsigned();
              $table->enum('type', config('constants.enums.post_type'));
            $table->string('text')->nullable();
            $table->enum('alignment',['left','center','right'])->nullable();
            $table->enum('color',['0','1']);
            $table->string('caption')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('media_url',500)->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('text_location')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_profile_id')->references('id')->on('user_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
