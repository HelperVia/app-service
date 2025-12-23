<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {--fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Live Support Install';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting installation...');

        $this->call($this->option('fresh') ? 'migrate:fresh' : 'migrate', ['--force' => true]);
        $this->info('Migrations completed.');
        $this->call('config:clear');
        $this->call('passport:fix-uuid');
        $this->call('db:seed', ['--force' => true]);
        $this->info('Seeding completed.');

        $this->info('🎉 Installation completed successfully!');
    }
}
