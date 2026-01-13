<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('data/users.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        $file = fopen($csvFile, 'r');
        
        // Skip header row
        $header = fgetcsv($file);
        
        // Read and insert each row
        while (($row = fgetcsv($file)) !== false) {
            DB::table('users')->insert([
                'username' => $row[0],
                'password' => Hash::make($row[1]),
                'role' => $row[2],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        fclose($file);
        
        $this->command->info('Users seeded successfully from CSV!');
    }
}
