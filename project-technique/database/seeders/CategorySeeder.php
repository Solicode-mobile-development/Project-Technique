<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('data/categories.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        $file = fopen($csvFile, 'r');
        
        // Skip header row
        $header = fgetcsv($file);
        
        // Read and insert each row
        while (($row = fgetcsv($file)) !== false) {
            DB::table('categories')->insert([
                'name' => $row[0],
                'slug' => $row[1],
                'description' => $row[2],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        fclose($file);
        
        $this->command->info('Categories seeded successfully from CSV!');
    }
}
