<?php

namespace App\Services\Outputs;

use App\Models\Trainee;
use App\Models\TraineeOutput;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class TraineeOutputService
{
    // Non-sensitive — meant to be shown on the public site once
    // published, unlike trainer/applicant documents.
    public const DISK = 'public';

    /**
     * @return Collection<int, TraineeOutput>
     */
    public function list(Trainee $trainee): Collection
    {
        return $trainee->outputs()->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Trainee $trainee, array $data): TraineeOutput
    {
        $data = $this->storeFileIfPresent($data);

        return $trainee->outputs()->create([
            ...$data,
            'project_id' => $trainee->project_id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(TraineeOutput $output, array $data): TraineeOutput
    {
        if (isset($data['work_file']) && $output->work_file_path) {
            Storage::disk(self::DISK)->delete($output->work_file_path);
        }

        $output->update($this->storeFileIfPresent($data));

        return $output;
    }

    /**
     * status and score are deliberately absent from TraineeOutput's
     * #[Fillable] list — grading isn't something update() should ever be
     * able to mass-assign.
     *
     * @param  array<string, mixed>  $data
     */
    public function review(TraineeOutput $output, array $data): TraineeOutput
    {
        $output->forceFill($data)->save();

        return $output;
    }

    public function delete(TraineeOutput $output): void
    {
        if ($output->work_file_path) {
            Storage::disk(self::DISK)->delete($output->work_file_path);
        }

        $output->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function storeFileIfPresent(array $data): array
    {
        if (isset($data['work_file'])) {
            $data['work_file_path'] = $data['work_file']->store('trainee-outputs', self::DISK);
        }
        unset($data['work_file']);

        return $data;
    }
}
