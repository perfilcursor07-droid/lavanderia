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
        Schema::table('coleta_pecas', function (Blueprint $table) {
            $table->integer('quantidade_empacotada')->default(0)->after('peso');
            $table->decimal('peso_empacotado', 8, 2)->default(0)->after('quantidade_empacotada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coleta_pecas', function (Blueprint $table) {
            $table->dropColumn(['quantidade_empacotada', 'peso_empacotado']);
        });
    }
};
