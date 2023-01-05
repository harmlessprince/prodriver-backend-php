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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('declined_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('file_id')->nullable()->constrained('files');
            $table->morphs('documentable');
            $table->string('document_type')->nullable(); //guarantor_id_card, id_card,
            $table->string('status')->nullable(); //submitted, accepted, declined
            $table->text('reason')->nullable(); //reason for declining id
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
        Schema::dropIfExists('documents');
    }
};
