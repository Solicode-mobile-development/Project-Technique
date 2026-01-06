<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = fopen(database_path('data/users.csv'), 'r');
        $firstLine = true;

        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if ($firstLine) {
                $firstLine = false;
                continue;
            }

            \App\Models\User::create([
                'name' => $data[0],
                'username' => $data[1],
                'email' => $data[2],
                'password' => bcrypt($data[3]),
                'role' => $data[4],
            ]);
        }

        fclose($csvFile);
    }
}
