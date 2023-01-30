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
            $table->foreignId('truck_type_id')->nullable()->constrained('truck_types')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->string('registration_number')->nullable();
            $table->string('tonnage_id')->nullable();
            $table->string('chassis_number')->nullable()->unique();
            $table->string('maker')->nullable();
            $table->string('model')->nullable();
            $table->boolean('on_trip')->default(false);
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
