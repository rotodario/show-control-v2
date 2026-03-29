<?php

namespace App\Support;

use App\Models\SharedAccess;
use App\Models\Show;
use App\Models\ShowMessageRead;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ShowMessageReadService
{
    public function markReadForUser(Show $show, User $user): void
    {
        $this->persistReadMarker([
            'show_id' => $show->id,
            'user_id' => $user->id,
        ], [
            'shared_access_id' => null,
        ]);
    }

    public function markReadForSharedAccess(Show $show, SharedAccess $grant): void
    {
        $this->persistReadMarker([
            'show_id' => $show->id,
            'shared_access_id' => $grant->id,
        ], [
            'user_id' => null,
        ]);
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

    public function unreadMessageIdsForUser(Show $show, User $user): Collection
    {
        $read = ShowMessageRead::query()
            ->where('show_id', $show->id)
            ->where('user_id', $user->id)
            ->first();

        return $this->unreadMessageIds($show, $read?->last_read_at, null);
    }

    public function unreadMessageIdsForSharedAccess(Show $show, SharedAccess $grant, array $sections): Collection
    {
        $read = ShowMessageRead::query()
            ->where('show_id', $show->id)
            ->where('shared_access_id', $grant->id)
            ->first();

        return $this->unreadMessageIds($show, $read?->last_read_at, $sections);
    }

    private function buildUnreadCounts(Collection $shows, Collection $readsByShowId, ?array $sections): Collection
    {
        return $shows->mapWithKeys(function (Show $show) use ($readsByShowId, $sections): array {
            $read = $readsByShowId->get($show->id);
            $count = $this->unreadMessageIds($show, $read?->last_read_at, $sections)->count();

            return [$show->id => $count];
        });
    }

    private function unreadMessageIds(Show $show, mixed $lastReadAt, ?array $sections): Collection
    {
        $messages = $show->relationLoaded('sectionMessages')
            ? $show->sectionMessages
            : $show->sectionMessages()->get();

        if ($sections !== null) {
            $messages = $messages->whereIn('section', $sections)->values();
        }

        return $messages
            ->filter(fn ($message) => $lastReadAt === null || $message->created_at->gt($lastReadAt))
            ->pluck('id')
            ->values();
    }

    private function persistReadMarker(array $attributes, array $values): void
    {
        try {
            ShowMessageRead::updateOrCreate(
                $attributes,
                [
                    ...$values,
                    'last_read_at' => $this->safeReadTimestamp(),
                ]
            );
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function safeReadTimestamp(): string
    {
        $timestamp = Carbon::now();

        if ($timestamp->format('H') === '02') {
            $timestamp = $timestamp->addHour();
        }

        return $timestamp->format('Y-m-d H:i:s');
    }
}
