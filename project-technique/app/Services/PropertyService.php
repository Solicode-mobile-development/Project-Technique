<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyService
{
    public function getAllProperties(array $filters = [])
    {
        $query = Property::with('category');

        if (!empty($filters['city'])) {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        return $query->latest()->paginate(12);
    }

    public function createProperty(array $data): Property
    {
        DB::beginTransaction();
        try {
            if (isset($data['image']) && $data['image']->isValid()) {
                $path = $data['image']->store('properties', 'public');
                $data['image_path'] = $path;
            }

            $property = Property::create($data);
            DB::commit();
            return $property;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Property creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateProperty(Property $property, array $data): Property
    {
        DB::beginTransaction();
        try {
            if (isset($data['image']) && $data['image']->isValid()) {
                // Delete old image if exists
                if ($property->image_path && Storage::disk('public')->exists($property->image_path)) {
                    Storage::disk('public')->delete($property->image_path);
                }

                $path = $data['image']->store('properties', 'public');
                $data['image_path'] = $path;
            }

            $property->update($data);
            DB::commit();
            return $property;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Property update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProperty(Property $property): bool
    {
        DB::beginTransaction();
        try {
            // Delete image if exists
            if ($property->image_path && Storage::disk('public')->exists($property->image_path)) {
                Storage::disk('public')->delete($property->image_path);
            }

            $property->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Property deletion failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function importFromCsv(string $filePath): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file);

        // Validate CSV headers
        $expectedHeaders = ['title', 'description', 'price', 'city', 'category'];
        if (array_diff($expectedHeaders, $headers)) {
            throw new \Exception('Invalid CSV format. Expected headers: ' . implode(', ', $expectedHeaders));
        }

        while (($row = fgetcsv($file)) !== false) {
            try {
                $data = array_combine($headers, $row);

                // Find or create category
                $category = Category::firstOrCreate(
                    ['name' => trim($data['category'])],
                    ['slug' => \Str::slug(trim($data['category']))]
                );

                Property::create([
                    'title' => trim($data['title']),
                    'description' => trim($data['description']),
                    'price' => (float) trim($data['price']),
                    'city' => trim($data['city']),
                    'category_id' => $category->id,
                ]);

                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Row " . ($results['success'] + $results['failed']) . ": " . $e->getMessage();
            }
        }

        fclose($file);
        return $results;
    }

    public function getPropertyStats(): array
    {
        return [
            'total' => Property::count(),
            'average_price' => Property::avg('price'),
            'cities' => Property::select('city', DB::raw('COUNT(*) as count'))
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
            'categories' => Category::withCount('properties')->get()
        ];
    }
}
