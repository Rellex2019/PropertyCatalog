<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ->take(50)
            ->get();

        return Inertia::render('Catalog/Index', [
            'properties' => $properties,
            'total' => Property::active()->count(),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = Property::active();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->input('type') && $request->input('type') !== 'all') {
            $query->where('type', $request->input('type'));
        }

        if ($request->input('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priceMin') && is_numeric($request->input('priceMin'))) {
            $query->where('price', '>=', $request->input('priceMin'));
        }

        if ($request->filled('priceMax') && is_numeric($request->input('priceMax'))) {
            $query->where('price', '<=', $request->input('priceMax'));
        }

        if ($request->input('rooms') && $request->input('rooms') !== 'all') {
            $rooms = (int) $request->input('rooms');

            if ($rooms >= 4) {
                $query->where('rooms', '>=', 4);
            } else {
                $query->where('rooms', $rooms);
            }
        }

        $query->latest('published_at');

        $total = (clone $query)->count();
        $properties = $query->take(50)->get();

        return response()->json([
            'properties' => $properties,
            'total' => $total,
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
