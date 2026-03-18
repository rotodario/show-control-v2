<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('show_message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shared_access_id')->nullable()->constrained('shared_accesses')->nullOnDelete();
            $table->timestamp('last_read_at');
            $table->timestamps();

            $table->unique(['show_id', 'user_id']);
            $table->unique(['show_id', 'shared_access_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('show_message_reads');
    }
};
