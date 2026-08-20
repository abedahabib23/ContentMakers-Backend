<?php

namespace App\Http\Resources;

use App\Models\Certificate;
use App\Services\Certificates\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Certificate
 */
class CertificateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trainee' => [
                'id' => $this->trainee->id,
                'name' => $this->trainee->user->name,
            ],
            'project_id' => $this->project_id,
            'certificate_number' => $this->certificate_number,
            'file_url' => $this->file_path
                ? Storage::disk(CertificateService::DISK)->url($this->file_path)
                : null,
            'issued_at' => $this->issued_at,
            'created_at' => $this->created_at,
        ];
    }
}
