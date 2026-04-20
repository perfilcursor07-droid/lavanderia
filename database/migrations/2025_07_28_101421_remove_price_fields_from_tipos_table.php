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
        Schema::table('tipos', function (Blueprint $table) {
            $table->dropColumn('preco_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipos', function (Blueprint $table) {
            $table->decimal('preco_kg', 8, 2)->default(0);
        });
    }
};
