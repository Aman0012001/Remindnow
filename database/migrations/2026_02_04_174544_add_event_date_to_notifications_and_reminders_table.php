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
        Schema::table('notifications', function (Blueprint $table) {
            $table->date('event_date')->nullable()->after('description');
        });

        Schema::table('calendar_event_reminders', function (Blueprint $table) {
            $table->date('event_date')->nullable()->after('event_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('event_date');
        });

        Schema::table('calendar_event_reminders', function (Blueprint $table) {
            $table->dropColumn('event_date');
        });
    }
};
