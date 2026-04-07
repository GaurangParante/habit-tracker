<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('habit_logs', function (Blueprint $table) {
            $table->unsignedInteger('count')->default(0)->after('status');
            $table->index('date');
        });

        DB::table('habit_logs')->update([
            'count' => DB::raw('CASE WHEN status = 1 THEN 1 ELSE 0 END'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habit_logs', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropColumn('count');
        });
    }
};
