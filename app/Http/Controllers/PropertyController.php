<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function index(): Response
    {
        $properties = Property::active()
            ->latest('published_at')
            ->get();

        $statistics = [
            'total' => Property::active()->count(),
            'sold' => Property::where('status', 'Продано')->count(),
            'featured' => Property::featured()->count(),
        ];

        return Inertia::render('Catalog/Index', [
            'properties' => $properties,
            'statistics' => $statistics,
        ]);
    }

    public function show(Property $property): Response
    {
        $similarProperties = Property::active()
            ->where('type', $property->type)
            ->where('id', '!=', $property->id)
            ->limit(3)
            ->get();

        return Inertia::render('Catalog/Show', [
            'property' => $property,
            'similarProperties' => $similarProperties,
            'propertyUrl' => route('catalog.show', $property->id),
        ]);
    }
}