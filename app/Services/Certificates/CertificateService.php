<?php

namespace App\Services\Certificates;

use App\Models\Certificate;
use App\Models\Trainee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    // Meant to be shared/verified — the `public` disk.
    public const DISK = 'public';

    /**
     * @return Collection<int, Certificate>
     */
    public function list(Trainee $trainee): Collection
    {
        return $trainee->certificates()->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Trainee $trainee, array $data): Certificate
    {
        if (isset($data['file'])) {
            $data['file_path'] = $data['file']->store('certificates', self::DISK);
        }
        unset($data['file']);

        return $trainee->certificates()->create([
            ...$data,
            'project_id' => $trainee->project_id,
            'certificate_number' => $this->generateUniqueNumber(),
            'issued_at' => $data['issued_at'] ?? now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Certificate $certificate, array $data): Certificate
    {
        if (isset($data['file'])) {
            if ($certificate->file_path) {
                Storage::disk(self::DISK)->delete($certificate->file_path);
            }
            $data['file_path'] = $data['file']->store('certificates', self::DISK);
        }
        unset($data['file']);

        $certificate->update($data);

        return $certificate;
    }

    public function delete(Certificate $certificate): void
    {
        if ($certificate->file_path) {
            Storage::disk(self::DISK)->delete($certificate->file_path);
        }

        $certificate->delete();
    }

    private function generateUniqueNumber(): string
    {
        do {
            $number = 'CM-'.now()->format('Y').'-'.Str::upper(Str::random(8));
        } while (Certificate::where('certificate_number', $number)->exists());

        return $number;
    }
}
