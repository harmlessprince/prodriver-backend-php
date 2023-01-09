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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('trip_id')->unique();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('matched_by')->nullable()->constrained('users');
            $table->foreignId('declined_by')->nullable()->constrained('users');
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('truck_id')->constrained('trucks')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('cargo_owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('truck_owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('way_bill_picture_id')->nullable()->constrained('files');
            $table->string('status');
            $table->softDeletes();
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
        Schema::dropIfExists('trips');
    }
};
