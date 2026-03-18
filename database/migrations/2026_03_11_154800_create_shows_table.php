<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->string('city');
            $table->string('venue')->nullable();
            $table->string('name');
            $table->string('status')->default('tentative');
            $table->time('load_in_at')->nullable();
            $table->time('meal_at')->nullable();
            $table->time('soundcheck_at')->nullable();
            $table->time('doors_at')->nullable();
            $table->time('show_at')->nullable();
            $table->time('show_end_at')->nullable();
            $table->time('load_out_at')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_role')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('lighting_notes')->nullable();
            $table->boolean('lighting_validated')->default(false);
            $table->text('sound_notes')->nullable();
            $table->boolean('sound_validated')->default(false);
            $table->text('space_notes')->nullable();
            $table->boolean('space_validated')->default(false);
            $table->text('general_notes')->nullable();
            $table->boolean('general_validated')->default(false);
            $table->timestamps();

            $table->index(['date', 'tour_id']);
            $table->index(['status', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shows');
    }
};
