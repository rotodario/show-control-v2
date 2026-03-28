<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->string('flight_origin')->nullable()->after('travel_mode');
            $table->string('flight_destination')->nullable()->after('flight_origin');
            $table->string('flight_duration_estimate')->nullable()->after('flight_destination');
            $table->text('flight_notes')->nullable()->after('flight_duration_estimate');
        });
    }

    public function down(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropColumn([
                'flight_origin',
                'flight_destination',
                'flight_duration_estimate',
                'flight_notes',
            ]);
        });
    }
};
