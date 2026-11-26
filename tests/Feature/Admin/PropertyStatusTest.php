<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_rental_status_cannot_be_used_when_creating_property(): void
    {
        $admin = User::factory()->mainAdmin()->create();

        $response = $this->actingAs($admin)->post(route('admin.properties.store'), [
            'title' => 'Квартира в аренду',
            'description' => 'Описание',
            'price' => 5000000,
            'currency' => '₽',
            'address' => 'ул. Тестовая, 1',
            'city' => 'Москва',
            'area' => 50,
            'rooms' => 2,
            'floor' => 3,
            'total_floors' => 10,
            'type' => 'Квартира',
            'status' => 'Сдается',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseMissing('properties', ['title' => 'Квартира в аренду']);
    }

    public function test_sale_status_can_be_used_when_creating_property(): void
    {
        $admin = User::factory()->mainAdmin()->create();

        $response = $this->actingAs($admin)->post(route('admin.properties.store'), [
            'title' => 'Квартира на продажу',
            'description' => 'Описание',
            'price' => 5000000,
            'currency' => '₽',
            'address' => 'ул. Тестовая, 2',
            'city' => 'Москва',
            'area' => 50,
            'rooms' => 2,
            'floor' => 3,
            'total_floors' => 10,
            'type' => 'Квартира',
            'status' => 'Продается',
        ]);

        $response->assertRedirect(route('admin.properties.index'));
        $this->assertDatabaseHas('properties', [
            'title' => 'Квартира на продажу',
            'status' => 'Продается',
        ]);
    }
}
