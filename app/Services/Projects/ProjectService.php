<?php

namespace App\Services\Projects;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProjectService
{
    // Project images are non-sensitive (unlike trainer documents) — the
    // `public` disk, so they're directly viewable by URL.
    public const IMAGE_DISK = 'public';

    /**
     * @param  array{search?: string|null, trainer_id?: int|null}  $filters
     * @return LengthAwarePaginator<int, Project>
     */
    public function list(User $actor, array $filters = []): LengthAwarePaginator
    {
        $query = Project::with('trainer.user')->latest();

        // Without projects.view, a trainer only sees their own — viewAny
        // grants access to the endpoint, not to every trainer's projects.
        if (! $actor->can('projects.view')) {
            $query->whereHas('trainer', fn ($q) => $q->where('user_id', $actor->id));
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];

            // ilike, not like — Postgres LIKE is case-sensitive.
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('number', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if (! empty($filters['trainer_id'])) {
            $query->where('trainer_id', $filters['trainer_id']);
        }

        return $query->paginate();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Project
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $data['image']->store('projects', self::IMAGE_DISK);
        }
        unset($data['image']);

        return Project::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Project $project, array $data): Project
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($project->image_path) {
                Storage::disk(self::IMAGE_DISK)->delete($project->image_path);
            }
            $data['image_path'] = $data['image']->store('projects', self::IMAGE_DISK);
        }
        unset($data['image']);

        $project->update($data);

        return $project;
    }

    public function delete(Project $project): void
    {
        if ($project->image_path) {
            Storage::disk(self::IMAGE_DISK)->delete($project->image_path);
        }

        $project->delete();
    }
}
