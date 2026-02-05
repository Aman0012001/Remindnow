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
        Schema::create('calendar_event_reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('google_event_id');
            $table->string('event_title')->nullable();
            $table->integer('reminder_days');
            $table->dateTime('remind_at');
            $table->string('status')->default('pending'); // pending, sent
            $table->timestamps();

            $table->index(['user_id', 'google_event_id', 'reminder_days'], 'rem_user_evt_day');
            $table->index(['status', 'remind_at'], 'rem_status_time');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_event_reminders');
    }
};
