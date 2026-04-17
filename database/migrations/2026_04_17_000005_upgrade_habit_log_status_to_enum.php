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
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('habit_logs', function (Blueprint $table) use ($driver) {
            if ($driver === 'mysql') {
                $table->enum('status_state', ['pending', 'completed', 'missed'])->default('pending')->after('date');
            } else {
                $table->string('status_state', 20)->default('pending')->after('date');
            }
        });

        DB::table('habit_logs')->update([
            'status_state' => DB::raw("CASE WHEN status = 1 THEN 'completed' ELSE 'pending' END"),
        ]);

        Schema::table('habit_logs', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('habit_logs', function (Blueprint $table) {
            $table->renameColumn('status_state', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habit_logs', function (Blueprint $table) {
            $table->boolean('status_flag')->default(false)->after('date');
        });

        DB::table('habit_logs')->update([
            'status_flag' => DB::raw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END"),
        ]);

        Schema::table('habit_logs', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('habit_logs', function (Blueprint $table) {
            $table->renameColumn('status_flag', 'status');
        });
    }
};
