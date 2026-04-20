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
        if (!Schema::hasColumn('estabelecimentos', 'preco_peca')) {
            Schema::table('estabelecimentos', function (Blueprint $table) {
                $table->decimal('preco_peca', 8, 2)->default(0)->after('preco_kg');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estabelecimentos', function (Blueprint $table) {
            $table->dropColumn('preco_peca');
        });
    }
};
