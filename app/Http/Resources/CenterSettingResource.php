<?php

namespace App\Http\Resources;

use App\Models\CenterSetting;
use App\Services\CenterSettings\CenterSettingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin CenterSetting
 */
class CenterSettingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'city' => $this->city,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo_url' => $this->logo_path
                ? Storage::disk(CenterSettingService::LOGO_DISK)->url($this->logo_path)
                : null,
            'accepts_registrations' => $this->accepts_registrations,
            'auto_issue_certificate' => $this->auto_issue_certificate,
            'notify_email_on_new_request' => $this->notify_email_on_new_request,
            'updated_at' => $this->updated_at,
        ];
    }
}
