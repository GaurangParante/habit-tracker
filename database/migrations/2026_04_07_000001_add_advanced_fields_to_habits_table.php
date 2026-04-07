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
        Schema::table('habits', function (Blueprint $table) {
            $table->unsignedInteger('current_streak')->default(0)->after('frequency');
            $table->unsignedInteger('longest_streak')->default(0)->after('current_streak');
            $table->string('frequency_type')->default('daily')->after('longest_streak');
            $table->json('frequency_value')->nullable()->after('frequency_type');
            $table->unsignedInteger('target_per_day')->default(1)->after('frequency_value');
        });

        DB::table('habits')->update([
            'frequency_type' => DB::raw('frequency'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->dropColumn([
                'current_streak',
                'longest_streak',
                'frequency_type',
                'frequency_value',
                'target_per_day',
            ]);
        });
    }
};
