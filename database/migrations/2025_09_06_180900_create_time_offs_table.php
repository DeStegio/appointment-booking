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
        Schema::create('time_offs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            $table->foreign('provider_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->index(['provider_id', 'start_at'], 'provider_start_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_offs');
    }
};

