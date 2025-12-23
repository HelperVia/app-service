<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\CannedResponseService;

class CannedResponsesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $service = app(CannedResponseService::class);

        $service->truncate();
        $cannedResponses = config('canned_responses');
        if ($cannedResponses) {
            foreach ($cannedResponses as $value) {
                $service->create($value);
            }
        }

    }
}
