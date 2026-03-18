<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('show_section_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->cascadeOnDelete();
            $table->string('section', 32);
            $table->text('message');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shared_access_id')->nullable()->constrained('shared_accesses')->nullOnDelete();
            $table->string('author_name');
            $table->timestamps();

            $table->index(['show_id', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('show_section_messages');
    }
};
