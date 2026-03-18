<?php

namespace App\Support;

use App\Models\SharedAccess;
use App\Models\Show;
use App\Models\ShowMessageRead;
use App\Models\User;
use Illuminate\Support\Collection;

class ShowMessageReadService
{
    public function markReadForUser(Show $show, User $user): void
    {
        ShowMessageRead::updateOrCreate(
            [
                'show_id' => $show->id,
                'user_id' => $user->id,
            ],
            [
                'shared_access_id' => null,
                'last_read_at' => now(),
            ]
        );
    }

    public function markReadForSharedAccess(Show $show, SharedAccess $grant): void
    {
        ShowMessageRead::updateOrCreate(
            [
                'show_id' => $show->id,
                'shared_access_id' => $grant->id,
            ],
            [
                'user_id' => null,
                'last_read_at' => now(),
            ]
        );
    }

    public function unreadCountsForUser(iterable $shows, User $user): Collection
    {
        return $this->buildUnreadCounts(
            collect($shows),
            ShowMessageRead::query()
                ->where('user_id', $user->id)
                ->get()
                ->keyBy('show_id'),
            null
        );
    }

    public function unreadCountsForSharedAccess(iterable $shows, SharedAccess $grant, array $sections): Collection
    {
        return $this->buildUnreadCounts(
            collect($shows),
            ShowMessageRead::query()
                ->where('shared_access_id', $grant->id)
                ->get()
                ->keyBy('show_id'),
            $sections
        );
    }

    private function buildUnreadCounts(Collection $shows, Collection $readsByShowId, ?array $sections): Collection
    {
        return $shows->mapWithKeys(function (Show $show) use ($readsByShowId, $sections): array {
            $messages = $show->relationLoaded('sectionMessages')
                ? $show->sectionMessages
                : $show->sectionMessages()->get();

            if ($sections !== null) {
                $messages = $messages->whereIn('section', $sections)->values();
            }

            $read = $readsByShowId->get($show->id);
            $lastReadAt = $read?->last_read_at;

            $count = $messages->filter(function ($message) use ($lastReadAt) {
                return $lastReadAt === null || $message->created_at->gt($lastReadAt);
            })->count();

            return [$show->id => $count];
        });
    }
}
