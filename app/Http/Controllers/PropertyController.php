<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function home(): Response
    {
        $featured = Property::active()
            ->featured()
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($featured->count() < 3) {
            $featured = $featured
                ->merge(
                    Property::active()
                        ->whereNotIn('id', $featured->pluck('id'))
                        ->latest('published_at')
                        ->take(3 - $featured->count())
                        ->get(),
                )
                ->values();
        }

        $popularCities = Property::active()
            ->whereNotNull('city')
            ->selectRaw('city, COUNT(*) as count')
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(6)
            ->get()
            ->map(fn ($item) => [
                'name' => $item->city,
                'count' => (int) $item->count,
            ]);

        return Inertia::render('Home', [
            'featuredProperties' => $featured,
            'popularCities' => $popularCities,
            'statistics' => [
                'total' => Property::active()->count(),
                'for_sale' => Property::active()->where('status', 'Продается')->count(),
                'for_rent' => Property::active()->where('status', 'Сдается')->count(),
                'featured' => Property::active()->featured()->count(),
                'cities' => Property::active()->whereNotNull('city')->distinct()->count('city'),
            ],
        ]);
    }

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
