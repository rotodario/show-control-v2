<?php

namespace App\Support;

use App\Models\SharedAccess;
use App\Models\Show;
use App\Models\ShowDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SharedAccessService
{
    public function permissions(SharedAccess $grant): array
    {
        return match ($grant->role) {
            'admin' => [
                'create_shows' => true,
                'update_shows' => true,
                'delete_shows' => true,
                'upload_documents' => true,
                'update_documents' => true,
                'delete_documents' => true,
                'show_fields' => [
                    'tour_id',
                    'date',
                    'city',
                    'venue',
                    'name',
                    'status',
                    'load_in_at',
                    'meal_at',
                    'soundcheck_at',
                    'doors_at',
                    'show_at',
                    'show_end_at',
                    'load_out_at',
                    'contact_name',
                    'contact_role',
                    'contact_phone',
                    'contact_email',
                    'lighting_notes',
                    'lighting_validated',
                    'sound_notes',
                    'sound_validated',
                    'space_notes',
                    'space_validated',
                    'general_notes',
                    'general_validated',
                ],
            ],
            'project_manager' => [
                'create_shows' => true,
                'update_shows' => true,
                'delete_shows' => true,
                'upload_documents' => true,
                'update_documents' => true,
                'delete_documents' => true,
                'show_fields' => [
                    'tour_id',
                    'date',
                    'city',
                    'venue',
                    'name',
                    'status',
                    'load_in_at',
                    'meal_at',
                    'soundcheck_at',
                    'doors_at',
                    'show_at',
                    'show_end_at',
                    'load_out_at',
                    'contact_name',
                    'contact_role',
                    'contact_phone',
                    'contact_email',
                    'lighting_notes',
                    'lighting_validated',
                    'sound_notes',
                    'sound_validated',
                    'space_notes',
                    'space_validated',
                    'general_notes',
                    'general_validated',
                ],
            ],
            'stage_manager' => [
                'create_shows' => false,
                'update_shows' => true,
                'delete_shows' => false,
                'upload_documents' => true,
                'update_documents' => false,
                'delete_documents' => false,
                'show_fields' => [
                    'date',
                    'city',
                    'venue',
                    'name',
                    'status',
                    'load_in_at',
                    'meal_at',
                    'soundcheck_at',
                    'doors_at',
                    'show_at',
                    'show_end_at',
                    'load_out_at',
                    'contact_name',
                    'contact_role',
                    'contact_phone',
                    'contact_email',
                    'lighting_notes',
                    'lighting_validated',
                    'sound_notes',
                    'sound_validated',
                    'space_notes',
                    'space_validated',
                    'general_notes',
                    'general_validated',
                ],
            ],
            'lighting' => [
                'create_shows' => false,
                'update_shows' => true,
                'delete_shows' => false,
                'upload_documents' => true,
                'update_documents' => false,
                'delete_documents' => false,
                'show_fields' => [
                    'lighting_notes',
                    'lighting_validated',
                ],
            ],
            'sound' => [
                'create_shows' => false,
                'update_shows' => true,
                'delete_shows' => false,
                'upload_documents' => true,
                'update_documents' => false,
                'delete_documents' => false,
                'show_fields' => [
                    'sound_notes',
                    'sound_validated',
                ],
            ],
            default => [
                'create_shows' => false,
                'update_shows' => false,
                'delete_shows' => false,
                'upload_documents' => false,
                'update_documents' => false,
                'delete_documents' => false,
                'show_fields' => [],
            ],
        };
    }

    public function resolveActiveGrant(string $token): ?SharedAccess
    {
        $grant = SharedAccess::with('tour')->where('token', $token)->first();

        if (! $grant || $grant->isRevoked()) {
            return null;
        }

        $grant->forceFill(['last_used_at' => now()])->saveQuietly();

        return $grant;
    }

    public function visibleShowsQuery(SharedAccess $grant): Builder
    {
        $ownerId = $grant->ownerId();

        return Show::query()
            ->when($ownerId, fn (Builder $query) => $query->where('owner_id', $ownerId))
            ->with('tour')
            ->when($grant->tour_id, fn (Builder $query) => $query->where('tour_id', $grant->tour_id))
            ->orderBy('date')
            ->orderBy('city');
    }

    public function canViewShow(SharedAccess $grant, Show $show): bool
    {
        $ownerId = $grant->ownerId();

        if ($ownerId && $show->owner_id !== $ownerId) {
            return false;
        }

        if ($grant->tour_id && $show->tour_id !== $grant->tour_id) {
            return false;
        }

        return true;
    }

    public function sectionVisibility(SharedAccess $grant): array
    {
        return match ($grant->role) {
            'admin', 'project_manager' => [
                'contact' => true,
                'schedules' => true,
                'lighting' => true,
                'sound' => true,
                'space' => true,
                'general' => true,
                'alerts' => true,
                'documents' => true,
            ],
            'lighting' => [
                'contact' => false,
                'schedules' => true,
                'lighting' => true,
                'sound' => false,
                'space' => true,
                'general' => false,
                'alerts' => true,
                'documents' => true,
            ],
            'sound' => [
                'contact' => false,
                'schedules' => true,
                'lighting' => false,
                'sound' => true,
                'space' => true,
                'general' => false,
                'alerts' => true,
                'documents' => true,
            ],
            'stage_manager' => [
                'contact' => true,
                'schedules' => true,
                'lighting' => false,
                'sound' => false,
                'space' => true,
                'general' => true,
                'alerts' => true,
                'documents' => true,
            ],
            default => [
                'contact' => false,
                'schedules' => true,
                'lighting' => false,
                'sound' => false,
                'space' => true,
                'general' => false,
                'alerts' => true,
                'documents' => false,
            ],
        };
    }

    public function canCreateShows(SharedAccess $grant): bool
    {
        return $this->permissions($grant)['create_shows'];
    }

    public function canUpdateShow(SharedAccess $grant): bool
    {
        return $this->permissions($grant)['update_shows'];
    }

    public function canDeleteShow(SharedAccess $grant): bool
    {
        return $this->permissions($grant)['delete_shows'];
    }

    public function editableShowFields(SharedAccess $grant): array
    {
        return $this->permissions($grant)['show_fields'];
    }

    public function visibleDocuments(SharedAccess $grant, Collection $documents): Collection
    {
        $allowedTypes = $this->allowedDocumentTypes($grant);

        if ($allowedTypes === null) {
            return $documents;
        }

        return $documents->whereIn('document_type', $allowedTypes)->values();
    }

    public function canDownloadDocument(SharedAccess $grant, ShowDocument $document): bool
    {
        return $this->visibleDocuments($grant, collect([$document]))->isNotEmpty();
    }

    public function canUploadDocuments(SharedAccess $grant): bool
    {
        return $this->permissions($grant)['upload_documents'];
    }

    public function canAccessSectionMessages(SharedAccess $grant, string $section): bool
    {
        return $this->sectionVisibility($grant)[$section] ?? false;
    }

    public function canUpdateDocuments(SharedAccess $grant): bool
    {
        return $this->permissions($grant)['update_documents'];
    }

    public function canDeleteDocuments(SharedAccess $grant): bool
    {
        return $this->permissions($grant)['delete_documents'];
    }

    public function allowedDocumentTypes(SharedAccess $grant): ?array
    {
        return match ($grant->role) {
            'lighting' => ['Rider', 'Patch', 'Plano', 'Input List', 'Timing'],
            'sound' => ['Rider', 'Patch', 'Input List', 'Timing'],
            default => null,
        };
    }
}
