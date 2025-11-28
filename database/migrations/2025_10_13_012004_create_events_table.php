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
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // event_id (PK)

            // Foreign Keys
            // $table->foreignId('court_id')->constrained('lapangans')->onDelete('cascade');
            // $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');

            // Kolom Data Event
            $table->string('nama_event');
            $table->string('lokasi');
            $table->text('deskripsi');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai')->nullable();
            $table->integer('biaya_pendaftaran')->default(0);
            $table->string('status')->default('upcoming'); // Contoh: upcoming, full, finished
            $table->string('gambar')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
