<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('lapangan_id')->nullable()->after('id');
            $table->foreign('lapangan_id')->references('id')->on('lapangans')->onDelete('set null');

            // Ubah lokasi jadi nullable (tidak wajib)
            $table->string('lokasi')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['lapangan_id']);
            $table->dropColumn('lapangan_id');

            $table->string('lokasi')->nullable(false)->change();
        });
    }
};
