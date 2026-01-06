<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Category;
use App\Services\PropertyService;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['city', 'category_id', 'min_price', 'max_price']);
        $properties = $this->propertyService->getAllProperties($filters);
        $categories = Category::all();

        // Get unique cities for filter dropdown
        $cities = Property::select('city')->distinct()->orderBy('city')->pluck('city');

        return view('public.properties.index', compact('properties', 'categories', 'cities'));
    }

    public function show(Property $property)
    {
        $property->load('category');
        $relatedProperties = Property::where('category_id', $property->category_id)
            ->where('id', '!=', $property->id)
            ->limit(4)
            ->get();

        return view('public.properties.show', compact('property', 'relatedProperties'));
    }

    public function search(Request $request)
    {
        $filters = $request->only(['city', 'category_id', 'min_price', 'max_price', 'search']);

        $query = Property::with('category');

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('city', 'like', '%' . $filters['search'] . '%');
            });
        }

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

        $properties = $query->latest()->paginate(12);

        if ($request->ajax()) {
            return view('public.properties.partials.properties-grid', compact('properties'))->render();
        }

        return response()->json([
            'html' => view('public.properties.partials.properties-grid', compact('properties'))->render(),
            'total' => $properties->total()
        ]);
    }
}
