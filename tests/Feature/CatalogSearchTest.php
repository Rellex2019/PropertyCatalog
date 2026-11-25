<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_search_applies_filters_and_excludes_inactive_properties(): void
    {
        $match = Property::factory()->create([
            'title' => 'Светлая квартира на Тверской',
            'address' => 'Тверская, 12',
            'type' => 'Квартира',
            'status' => 'Продается',
            'price' => 12000000,
            'rooms' => 2,
            'is_active' => true,
        ]);

        Property::factory()->create([
            'title' => 'Загородный дом',
            'type' => 'Дом',
            'status' => 'Продается',
            'price' => 12000000,
            'rooms' => 2,
            'is_active' => true,
        ]);

        Property::factory()->create([
            'title' => 'Светлая квартира архив',
            'type' => 'Квартира',
            'status' => 'Продается',
            'price' => 12000000,
            'rooms' => 2,
            'is_active' => false,
        ]);

        $response = $this->getJson('/catalog/search?'.http_build_query([
            'search' => 'Тверской',
            'type' => 'Квартира',
            'status' => 'Продается',
            'priceMin' => 10000000,
            'priceMax' => 13000000,
            'rooms' => 2,
        ]));

        $response->assertOk()
            ->assertJsonCount(1, 'properties')
            ->assertJsonPath('properties.0.id', $match->id)
            ->assertJsonPath('total', 1);
    }

    public function test_catalog_search_treats_four_rooms_as_or_more(): void
    {
        Property::factory()->create(['rooms' => 4, 'is_active' => true]);
        Property::factory()->create(['rooms' => 5, 'is_active' => true]);
        Property::factory()->create(['rooms' => 3, 'is_active' => true]);

        $this->getJson('/catalog/search?rooms=4')
            ->assertOk()
            ->assertJsonCount(2, 'properties')
            ->assertJsonPath('total', 2);
    }
}
