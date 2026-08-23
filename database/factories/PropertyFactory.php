<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $types = ['Квартира', 'Дом', 'Таунхаус', 'Коммерческая', 'Земельный участок'];
        $statuses = ['Продается', 'Сдается', 'Продано'];
        $cities = ['Москва', 'Санкт-Петербург', 'Казань', 'Новосибирск', 'Екатеринбург', 'Сочи'];
        
        $price = $this->faker->numberBetween(5000000, 100000000);
        
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $price,
            'currency' => '₽',
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->randomElement($cities),
            'area' => $this->faker->numberBetween(30, 300),
            'rooms' => $this->faker->numberBetween(1, 6),
            'floor' => $this->faker->numberBetween(1, 20),
            'total_floors' => $this->faker->numberBetween(1, 25),
            'type' => $this->faker->randomElement($types),
            'status' => $this->faker->randomElement($statuses),
            'image' => $this->faker->imageUrl(800, 600, 'property', true),
            'images' => [
                $this->faker->imageUrl(800, 600, 'property', true),
                $this->faker->imageUrl(800, 600, 'property', true),
                $this->faker->imageUrl(800, 600, 'property', true),
            ],
            'amenities' => $this->faker->randomElements([
                'Парковка', 'Лифт', 'Консьерж', 'Охрана', 'Детская площадка',
                'Спортзал', 'Бассейн', 'Сауна', 'Wi-Fi', 'Кондиционер',
                'Балкон', 'Терраса', 'Камин', 'Система умный дом'
            ], 4),
            'is_featured' => $this->faker->boolean(20),
            'is_active' => true,
            'published_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}