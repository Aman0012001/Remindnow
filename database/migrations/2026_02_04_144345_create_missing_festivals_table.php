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
        if (!Schema::hasTable('festivals')) {
            Schema::create('festivals', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->text('date')->nullable(); // Can be CSV
                $table->string('image')->nullable();
                $table->text('temple_id')->nullable(); // JSON
                $table->text('states')->nullable(); // JSON
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
                $table->integer('is_popular')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('festival_description')) {
            Schema::create('festival_description', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id')->nullable();
                $table->integer('language_id')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('temples')) {
            Schema::create('temples', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('image')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('temple_description')) {
            Schema::create('temple_description', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id')->nullable();
                $table->integer('language_id')->nullable();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('faqs')) {
            Schema::table('faqs', function (Blueprint $table) {
                if (!Schema::hasColumn('faqs', 'is_festival')) {
                    $table->integer('is_festival')->default(0)->after('id');
                }
                if (!Schema::hasColumn('faqs', 'festival_id')) {
                    $table->integer('festival_id')->nullable()->after('is_festival');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('festival_description');
        Schema::dropIfExists('festivals');
        Schema::dropIfExists('temple_description');
        Schema::dropIfExists('temples');
        Schema::dropIfExists('settings');
    }
};
