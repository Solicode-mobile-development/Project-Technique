<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = fopen(database_path('data/properties.csv'), 'r');
        $firstLine = true;

        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if ($firstLine) {
                $firstLine = false;
                continue;
            }

            $user = \App\Models\User::where('username', $data[5])->first();
            $category = \App\Models\Category::where('slug', $data[6])->first();

            if ($user && $category) {
                \App\Models\Property::create([
                    'title' => $data[0],
                    'description' => $data[1],
                    'price' => $data[2],
                    'city' => $data[3],
                    'image_path' => empty($data[4]) ? null : $data[4],
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                ]);
            }
        }

        fclose($csvFile);
    }
}
