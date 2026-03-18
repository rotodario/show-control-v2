<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->string('external_source')->nullable()->after('owner_id');
            $table->string('external_calendar_id')->nullable()->after('external_source');
            $table->string('external_event_id')->nullable()->after('external_calendar_id');

            $table->unique(
                ['owner_id', 'external_source', 'external_calendar_id', 'external_event_id'],
                'shows_external_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropUnique('shows_external_unique');
            $table->dropColumn([
                'external_source',
                'external_calendar_id',
                'external_event_id',
            ]);
        });
    }
};
