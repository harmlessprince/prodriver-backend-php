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
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tonnage_id')->constrained('tonnages');
            $table->foreignId('cargo_owner_id')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('matched_by')->nullable()->constrained('users');
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->float('amount_willing_to_pay')->nullable();
            $table->float('potential_payout')->nullable();
            $table->boolean('display_amount_willing_to_pay')->default(true);
            $table->integer('number_of_trucks')->default(1);
            $table->text('description');
            $table->string('pickup_address');
            $table->string('destination_address');
            $table->date('date_needed');
            $table->string('financial_status');
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
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
