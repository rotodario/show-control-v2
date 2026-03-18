<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->string('action');
            $table->text('detail');
            $table->foreignId('tour_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('show_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('subject');
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
