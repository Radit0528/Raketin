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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Event
            $table->unsignedBigInteger('event_id')->nullable();

            // Lapangan
            $table->unsignedBigInteger('lapangan_id')->nullable();
            $table->date('tanggal')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->integer('durasi')->nullable();

            // Customer Info
            $table->string('nama');
            $table->string('email');
            $table->string('phone');

            // Payment
            $table->integer('amount'); // total harga
            $table->enum('status_pembayaran', ['pending', 'success', 'failed', 'challenge'])
                ->default('pending');

            $table->timestamps();

            // Relations
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('lapangan_id')->references('id')->on('lapangans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
