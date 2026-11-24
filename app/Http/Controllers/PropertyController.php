<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function sitemap(): \Illuminate\Http\Response
    {
        $urls = [
            ['loc' => route('home'), 'lastmod' => now()->toAtomString(), 'priority' => '1.0'],
            ['loc' => route('catalog.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
        ];

        Property::active()->latest('updated_at')->get()->each(function (Property $property) use (&$urls) {
            $urls[] = [
                'loc' => route('catalog.show', $property->id),
                'lastmod' => $property->updated_at?->toAtomString() ?? now()->toAtomString(),
                'priority' => '0.7',
            ];
        });

        $escape = fn ($value) => htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $item) {
            $xml .= '<url>';
            $xml .= '<loc>'.$escape($item['loc']).'</loc>';
            $xml .= '<lastmod>'.$escape($item['lastmod']).'</lastmod>';
            $xml .= '<priority>'.$escape($item['priority']).'</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

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
