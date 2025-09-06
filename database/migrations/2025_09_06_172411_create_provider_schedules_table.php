<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('provider_schedules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('provider_id');
                $table->unsignedTinyInteger('weekday')->index(); // 0=Sunday..6=Saturday
                $table->time('start_time');
                $table->time('end_time');
                $table->unsignedSmallInteger('slot_interval_minutes')->default(30);
                $table->timestamps();

                $table->foreign('provider_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');

                $table->index(['provider_id', 'weekday'], 'provider_weekday_index');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('provider_schedules');
        }
    };
