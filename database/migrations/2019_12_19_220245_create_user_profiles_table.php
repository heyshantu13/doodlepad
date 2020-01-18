<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('user_profiles', function (Blueprint $table) {
             $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->mediumText('bio');
            $table->text('profile_pic_url');
            $table->text('doodle_url');
            $table->date('date_of_birth');
            $table->boolean('is_private')->default('0');
            $table->string('fcm_registration_id')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->timestamps();
                $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('user_profiles');
    }
}
