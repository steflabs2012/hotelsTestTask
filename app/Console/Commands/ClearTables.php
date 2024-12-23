<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearTables extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'tables:clear';

    public function handle()
    {
        $tables = ['hotels', 'rooms', 'room_board', 'room_types', 'regions', 'regions_main', 'regions_sub', 'pricing_periods'];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->info("Table {$table} has been cleared.");
        }

        $this->info('All specified tables have been cleared successfully.');
    }
}
