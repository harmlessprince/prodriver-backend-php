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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('type');
            $table->string('mimetype')->nullable();
            $table->string('url')->nullable();
            $table->string('path')->nullable();
            $table->string('provider', 50)->nullable();
            $table->string('owner_type')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->integer('creator_id')->index();
            $table->longText('meta_data')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['owner_type', 'owner_id'], 'owner_type_owner_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
};
