<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_mail_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('notifications_enabled')->default(false);
            $table->string('from_name')->nullable();
            $table->string('reply_to_email')->nullable();
            $table->text('recipients')->nullable();
            $table->text('cc_recipients')->nullable();
            $table->string('subject_template')->nullable();
            $table->text('body_template')->nullable();
            $table->text('signature')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_mail_settings');
    }
};
