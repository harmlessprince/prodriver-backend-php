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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('registration_number')->nullable();
            $table->string('tonnage')->nullable();
            $table->string('chassis_number');
            $table->string('maker')->nullable();
            $table->string('model')->nullable();
            $table->foreignId('picture_id')->nullable()->constrained('files')->cascadeOnDelete();
            $table->foreignId('proof_of_ownership_id')->nullable()->constrained('files')->cascadeOnDelete();
            $table->foreignId('road_worthiness_id')->nullable()->constrained('files')->cascadeOnDelete();
            $table->foreignId('license_id')->nullable()->constrained('files')->cascadeOnDelete();
            $table->foreignId('insurance_id')->nullable()->constrained('files')->cascadeOnDelete();
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
        Schema::dropIfExists('trucks');
    }
};
