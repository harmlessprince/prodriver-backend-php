<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_next_of_kins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_phone_number')->nullable();
            $table->string('next_of_kin_email_address')->nullable();
            $table->string('next_of_kin_relationship')->nullable();
            $table->string('next_of_kin_occupation')->nullable();
            $table->string('next_of_kin_home_address')->nullable();
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
        Schema::dropIfExists('user_next_ofkins');
    }
};
