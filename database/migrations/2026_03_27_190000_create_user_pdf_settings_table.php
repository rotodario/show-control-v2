<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_pdf_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('brand_name')->nullable();
            $table->string('primary_color', 7)->default('#0f172a');
            $table->string('header_text')->nullable();
            $table->string('footer_text')->nullable();
            $table->boolean('show_generated_at')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pdf_settings');
    }
};
