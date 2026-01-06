<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use App\Models\Category;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function index()
    {
        $properties = $this->propertyService->getAllProperties(request()->all());
        $categories = Category::all();
        $stats = $this->propertyService->getPropertyStats();

        return view('admin.properties.index', compact('properties', 'categories', 'stats'));
    }

    public function store(PropertyRequest $request): JsonResponse
    {
        try {
            $property = $this->propertyService->createProperty($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully!',
                'property' => $property->load('category')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create property: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Property $property): JsonResponse
    {
        return response()->json([
            'property' => $property->load('category')
        ]);
    }

    public function update(PropertyRequest $request, Property $property): JsonResponse
    {
        try {
            $updatedProperty = $this->propertyService->updateProperty($property, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully!',
                'property' => $updatedProperty->load('category')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Property $property): JsonResponse
    {
        try {
            $this->propertyService->deleteProperty($property);

            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete property: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importCsv(Request $request): JsonResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $path = $request->file('csv_file')->store('temp');
            $fullPath = storage_path('app/' . $path);

            $results = $this->propertyService->importFromCsv($fullPath);

            // Clean up temp file
            Storage::delete($path);

            return response()->json([
                'success' => true,
                'message' => 'CSV import completed!',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'CSV import failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
