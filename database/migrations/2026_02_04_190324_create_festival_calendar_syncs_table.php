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
        Schema::create('festival_calendar_syncs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('festival_id');
            $table->string('google_event_id');
            $table->string('calendar_id')->default('primary');
            $table->timestamp('synced_at');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('festival_id')->references('id')->on('festivals')->onDelete('cascade');

            // Indexes
            $table->index('user_id');
            $table->index('festival_id');
            $table->unique(['user_id', 'festival_id', 'calendar_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('festival_calendar_syncs');
    }
};
