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
        if (!Schema::hasColumn('coletas', 'valor_total')) {
            Schema::table('coletas', function (Blueprint $table) {
                $table->decimal('valor_total', 10, 2)->default(0)->after('peso_total');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coletas', function (Blueprint $table) {
            $table->dropColumn('valor_total');
        });
    }
};
