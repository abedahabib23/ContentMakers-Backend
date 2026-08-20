<?php

namespace App\Services\Trainers;

use App\Models\Trainer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TrainerFileService
{
    // Sensitive documents (ID photo, CV, certificates) — the `private`
    // disk, never `public`. See CLAUDE.md's File Storage Architecture.
    // Public so TrainerFileController can stream downloads from the same
    // disk without hardcoding the name a second time.
    public const DISK = 'local';

    /**
     * @param  array{id_photo?: UploadedFile, cv?: UploadedFile, certificates?: array<int, UploadedFile>}  $files
     */
    public function upload(Trainer $trainer, array $files): Trainer
    {
        if (isset($files['id_photo'])) {
            $trainer->id_photo_path = $this->replace(
                $trainer->id_photo_path,
                $files['id_photo'],
                "trainers/{$trainer->id}/id-photo",
            );
        }

        if (isset($files['cv'])) {
            $trainer->cv_path = $this->replace(
                $trainer->cv_path,
                $files['cv'],
                "trainers/{$trainer->id}/cv",
            );
        }

        if (! empty($files['certificates'])) {
            $newPaths = array_map(
                fn (UploadedFile $file) => $file->store("trainers/{$trainer->id}/certificates", self::DISK),
                $files['certificates'],
            );

            $trainer->certificate_paths = [...($trainer->certificate_paths ?? []), ...$newPaths];
        }

        $trainer->save();

        return $trainer;
    }

    private function replace(?string $oldPath, UploadedFile $file, string $directory): string
    {
        if ($oldPath) {
            Storage::disk(self::DISK)->delete($oldPath);
        }

        return $file->store($directory, self::DISK);
    }
}
