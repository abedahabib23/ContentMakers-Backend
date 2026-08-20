<?php

namespace App\Services\CenterSettings;

use App\Models\CenterSetting;
use Illuminate\Support\Facades\Storage;

class CenterSettingService
{
    // Branding, not a sensitive document — the `public` disk.
    public const LOGO_DISK = 'public';

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(array $data): CenterSetting
    {
        $settings = CenterSetting::current();

        if (isset($data['logo'])) {
            if ($settings->logo_path) {
                Storage::disk(self::LOGO_DISK)->delete($settings->logo_path);
            }
            $data['logo_path'] = $data['logo']->store('center', self::LOGO_DISK);
        }
        unset($data['logo']);

        $settings->update($data);

        return $settings;
    }
}
