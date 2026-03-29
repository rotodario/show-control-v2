<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->string('public_summary_token', 64)->nullable()->unique()->after('city_longitude');
        });

        DB::table('shows')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($shows): void {
                foreach ($shows as $show) {
                    DB::table('shows')
                        ->where('id', $show->id)
                        ->update([
                            'public_summary_token' => Str::lower(Str::random(32)),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropUnique(['public_summary_token']);
            $table->dropColumn('public_summary_token');
        });
    }
};
