<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Client;
use Illuminate\Console\Command;
class PassportSeeder extends Seeder
{
    public function run()
    {

        if (!file_exists(storage_path('oauth-private.key'))) {
            Artisan::call('passport:keys');
            $this->command->info('✅ Keys created');
        }


        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => env('APP_NAME'),
            '--no-interaction' => true
        ]);

    }
}
