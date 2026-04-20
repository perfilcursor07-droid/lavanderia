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
        Schema::table('pesagens', function (Blueprint $table) {
            $table->enum('status', ['rascunho', 'concluida'])->default('concluida')->after('local_pesagem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesagens', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
