<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('data/properties.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        $file = fopen($csvFile, 'r');
        
        // Skip header row
        $header = fgetcsv($file);
        
        // Read and insert each row
        while (($row = fgetcsv($file)) !== false) {
            // Find user by username
            $user = DB::table('users')->where('username', $row[5])->first();
            
            if (!$user) {
                $this->command->warn("User not found: {$row[5]}. Skipping property: {$row[0]}");
                continue;
            }
            
            // Insert property
            $propertyId = DB::table('properties')->insertGetId([
                'title' => $row[0],
                'description' => $row[1],
                'price' => $row[2],
                'city' => $row[3],
                'image_path' => $row[4],
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Handle categories (can be multiple, separated by |)
            if (!empty($row[6])) {
                $categoryNames = explode('|', $row[6]);
                
                foreach ($categoryNames as $categoryName) {
                    $category = DB::table('categories')->where('name', trim($categoryName))->first();
                    
                    if ($category) {
                        DB::table('category_property')->insert([
                            'category_id' => $category->id,
                            'property_id' => $propertyId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
        
        fclose($file);
        
        $this->command->info('Properties seeded successfully from CSV!');
    }
}