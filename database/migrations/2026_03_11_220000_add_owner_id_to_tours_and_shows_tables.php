<?php

use App\Models\Show;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        Schema::table('shows', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        $fallbackOwnerId = User::query()->orderBy('id')->value('id');

        Tour::query()
            ->select('id')
            ->with([
                'activityLogs' => fn ($query) => $query
                    ->whereNotNull('actor_id')
                    ->orderBy('created_at')
                    ->orderBy('id'),
            ])
            ->chunkById(100, function ($tours) use ($fallbackOwnerId): void {
                foreach ($tours as $tour) {
                    $ownerId = $tour->activityLogs->first()?->actor_id ?? $fallbackOwnerId;

                    if ($ownerId) {
                        DB::table('tours')->where('id', $tour->id)->update(['owner_id' => $ownerId]);
                    }
                }
            });

        Show::query()
            ->select('id', 'tour_id')
            ->with([
                'tour:id,owner_id',
                'activityLogs' => fn ($query) => $query
                    ->whereNotNull('actor_id')
                    ->orderBy('created_at')
                    ->orderBy('id'),
            ])
            ->chunkById(100, function ($shows) use ($fallbackOwnerId): void {
                foreach ($shows as $show) {
                    $ownerId = $show->tour?->owner_id
                        ?? $show->activityLogs->first()?->actor_id
                        ?? $fallbackOwnerId;

                    if ($ownerId) {
                        DB::table('shows')->where('id', $show->id)->update(['owner_id' => $ownerId]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_id');
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_id');
        });
    }
};
