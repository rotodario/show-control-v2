<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_mail_settings', function (Blueprint $table): void {
            $table->boolean('alert_notifications_enabled')->default(false)->after('notifications_enabled');
            $table->text('alert_recipients')->nullable()->after('signature');
            $table->text('alert_cc_recipients')->nullable()->after('alert_recipients');
            $table->string('alert_subject_template')->nullable()->after('alert_cc_recipients');
            $table->text('alert_body_template')->nullable()->after('alert_subject_template');
        });
    }

    public function down(): void
    {
        Schema::table('user_mail_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'alert_notifications_enabled',
                'alert_recipients',
                'alert_cc_recipients',
                'alert_subject_template',
                'alert_body_template',
            ]);
        });
    }
};
