<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_alert_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('core_info_enabled')->default(true);
            $table->unsignedSmallInteger('core_info_days')->default(90);
            $table->boolean('status_enabled')->default(true);
            $table->unsignedSmallInteger('status_days')->default(30);
            $table->boolean('validations_enabled')->default(true);
            $table->unsignedSmallInteger('validations_days')->default(7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_alert_settings');
    }
};
