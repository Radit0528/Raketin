<?php
// ... namespace dan use statements ...

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
    Schema::table('lapangans', function (Blueprint $table) {
        $table->foreignId('user_id')
              ->nullable() // sementara nullable karena data lama
              ->after('id')
              ->constrained('users')
              ->cascadeOnDelete();
    });
}

    public function down(): void
    {
        Schema::table('lapangans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};