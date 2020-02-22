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
            //  $table->bigIncrements('id');
            // $table->unsignedBigInteger('user_id');
            // $table->mediumText('bio');
            // $table->longText('profile_picture_url')->nullable();
            // $table->text('doodle_url')->nullable();
            // $table->date('date_of_birth');
            // $table->boolean('is_private')->default('0');
            // $table->string('fcm_registration_id')->nullable();
            // $table->enum('gender', ['male', 'female'])->nullable();
            // $table->timestamps();
            //     $table->foreign('user_id')
            //     ->references('id')
            //     ->on('users')
            //     ->onDelete('cascade');


          $table->increments('id');
            $table->string('user_id');
           $table->mediumText('bio')->nullable();
            $table->longText('profile_picture_url')->nullable();
            $table->text('doodle_url')->nullable();
            $table->date('date_of_birth');
            $table->boolean('is_private')->default('0');
            $table->string('fcm_registration_id')->nullable();
            $table->enum('gender', ['male', 'female']);
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
        Schema::dropIfExists('user_profiles');
    }
}
