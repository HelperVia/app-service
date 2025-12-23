<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixPassportUuid extends Command
{
    protected $signature = 'passport:fix-uuid';
    protected $description = 'Convert user_id columns to UUID in oauth tables';

    public function handle()
    {
        $prefix = DB::getTablePrefix();
        $database = DB::getDatabaseName();

        $tables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES 
                              WHERE TABLE_SCHEMA = '{$database}' 
                              AND TABLE_NAME LIKE '{$prefix}oauth_%'");

        foreach ($tables as $tableObj) {
            $table = $tableObj->TABLE_NAME;
            $tableWithoutPrefix = str_replace($prefix, '', $table);

            if (Schema::hasColumn($tableWithoutPrefix, 'user_id')) {
                $this->info("Converting {$table}.user_id to UUID...");

                try {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `user_id` CHAR(36)");
                    $this->info("✓ {$table} converted");
                } catch (\Exception $e) {
                    $this->error("✗ {$table} failed: " . $e->getMessage());
                }
            }
        }

        $this->info('Done!');
        return 0;
    }
}