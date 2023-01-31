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
            $table->foreignId('account_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('accepted_order_id')->nullable()->constrained('accepted_orders')->nullOnDelete();
            $table->foreignId('driver_id')->constrained('drivers');
            $table->foreignId('truck_id')->constrained('trucks');
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('cargo_owner_id')->constrained('users');
            $table->foreignId('transporter_id')->constrained('users');
            $table->foreignId('way_bill_picture_id')->nullable()->constrained('files')->nullOnDelete();
            $table->float('total_payout')->nullable();
            $table->float('advance_payout')->nullable();
            $table->float('margin_profit_amount')->nullable();
            $table->float('margin_profit_percentage')->nullable();
            $table->date('loading_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->foreignId('trip_status_id')->nullable()->constrained('trip_statuses')->nullOnDelete();
            $table->foreignId('way_bill_status_id')->nullable()->constrained('waybill_statuses')->nullOnDelete();
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
