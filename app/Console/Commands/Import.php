<?php

namespace App\Console\Commands;

use Dflydev\DotAccessData\Data;
use Exception;
use Illuminate\Console\Command;
use App\Services\ApiClient;
use App\Services\DataImporter;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from the API (hotels or regions or price)';

    /**
     * Execute the console command.
     * @return int
     * @throws Exception
     */
    public function handle(ApiClient $apiClient): int
    {
        $importer = new DataImporter($apiClient);

        $type = $this->argument('type');

        switch ($type) {
            case 'hotels':
                $importer->importHotels();
                $this->info('Hotels imported successfully.');
                break;

            case 'regions':
                $importer->importRegions();
                $this->info('Regions imported successfully.');

                $importer->importRegionsMain();
                $this->info('Main Regions imported successfully.');

                $importer->importRegionsSub();
                $this->info('Sub Regions imported successfully.');
                break;
            case 'price':
                $importer->importPricingPeriods();
                $this->info('Prices imported successfully.');
                break;

            default:
                $this->error('Invalid type. Please use "hotels" or "regions" or "price".');
                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
