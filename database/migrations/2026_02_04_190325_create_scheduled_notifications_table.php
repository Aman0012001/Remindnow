<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('festival_id');
            $table->string('title');
            $table->text('message');
            $table->timestamp('scheduled_at');
            $table->enum('status', ['pending', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('festival_id')->references('id')->on('festivals')->onDelete('cascade');

            // Indexes
            $table->index('user_id');
            $table->index('festival_id');
            $table->index('scheduled_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_notifications');
    }
};
