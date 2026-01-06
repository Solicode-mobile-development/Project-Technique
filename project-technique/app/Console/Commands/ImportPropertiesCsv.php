<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportPropertiesCsv extends Command
{
    protected $signature = 'properties:import-csv {file}';
    protected $description = 'Import properties from CSV file';

    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        parent::__construct();
        $this->propertyService = $propertyService;
    }

    public function handle(): int
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        try {
            $this->info('Starting CSV import...');

            $results = $this->propertyService->importFromCsv($filePath);

            $this->info("Import completed!");
            $this->info("Successfully imported: {$results['success']} properties");
            $this->info("Failed: {$results['failed']} properties");

            if (!empty($results['errors'])) {
                $this->warn("Errors encountered:");
                foreach ($results['errors'] as $error) {
                    $this->error($error);
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            Log::error('CSV import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
