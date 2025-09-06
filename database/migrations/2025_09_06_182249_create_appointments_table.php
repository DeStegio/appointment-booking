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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('service_id');

            $table->dateTime('start_at');
            $table->dateTime('end_at');

            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('provider_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('customer_id')
                ->references('id')->on('users')
                ->onDelete('restrict');

            $table->foreign('service_id')
                ->references('id')->on('services')
                ->onDelete('restrict');

            $table->unique(['provider_id', 'start_at'], 'provider_start_unique');
            $table->index(['provider_id', 'start_at'], 'provider_start_at_index');
            $table->index('provider_id');
            $table->index('customer_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
