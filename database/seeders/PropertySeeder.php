<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // Создаем 30 случайных объектов
        Property::factory(30)->create();
        
        // Создаем несколько конкретных объектов для демонстрации
        Property::create([
            'title' => 'Современная квартира в центре',
            'description' => 'Просторная трехкомнатная квартира в центре города. 
                              Отличная транспортная доступность, развитая инфраструктура. 
                              В доме есть подземный паркинг, консьерж-сервис, видеонаблюдение.',
            'price' => 25000000,
            'currency' => '₽',
            'address' => 'ул. Тверская, 15',
            'city' => 'Москва',
            'area' => 85.5,
            'rooms' => 3,
            'floor' => 7,
            'total_floors' => 12,
            'type' => 'Квартира',
            'status' => 'Продается',
            'image' => 'https://via.placeholder.com/800x600/4F46E5/FFFFFF?text=Modern+Apartment',
            'images' => [
                'https://via.placeholder.com/800x600/4F46E5/FFFFFF?text=Kitchen',
                'https://via.placeholder.com/800x600/4F46E5/FFFFFF?text=Living+Room',
                'https://via.placeholder.com/800x600/4F46E5/FFFFFF?text=Bedroom',
            ],
            'amenities' => ['Парковка', 'Лифт', 'Консьерж', 'Охрана', 'Wi-Fi'],
            'is_featured' => true,
            'is_active' => true,
            'published_at' => now(),
        ]);

        Property::create([
            'title' => 'Уютный дом с садом в Подмосковье',
            'description' => 'Двухэтажный дом с большим участком в живописном месте. 
                              Отличный вариант для семьи. Свой сад, беседка, гараж.',
            'price' => 45000000,
            'currency' => '₽',
            'address' => 'ул. Лесная, 7',
            'city' => 'Московская область',
            'area' => 180.0,
            'rooms' => 5,
            'floor' => 1,
            'total_floors' => 2,
            'type' => 'Дом',
            'status' => 'Продается',
            'image' => 'https://via.placeholder.com/800x600/10B981/FFFFFF?text=House+with+Garden',
            'images' => [
                'https://via.placeholder.com/800x600/10B981/FFFFFF?text=Garden',
                'https://via.placeholder.com/800x600/10B981/FFFFFF?text=Living+Room',
                'https://via.placeholder.com/800x600/10B981/FFFFFF?text=Kitchen',
            ],
            'amenities' => ['Парковка', 'Охрана', 'Детская площадка', 'Сауна', 'Балкон'],
            'is_featured' => false,
            'is_active' => true,
            'published_at' => now(),
        ]);

        Property::create([
            'title' => 'Студия в новостройке',
            'description' => 'Уютная студия в современном жилом комплексе. 
                              В шаговой доступности метро, супермаркеты, школы.',
            'price' => 12000000,
            'currency' => '₽',
            'address' => 'ул. Новослободская, 45',
            'city' => 'Москва',
            'area' => 35.0,
            'rooms' => 1,
            'floor' => 5,
            'total_floors' => 16,
            'type' => 'Квартира',
            'status' => 'Продается',
            'image' => 'https://via.placeholder.com/800x600/F59E0B/FFFFFF?text=Studio+Apartment',
            'images' => [
                'https://via.placeholder.com/800x600/F59E0B/FFFFFF?text=Studio+Room',
                'https://via.placeholder.com/800x600/F59E0B/FFFFFF?text=Kitchen+Area',
            ],
            'amenities' => ['Лифт', 'Консьерж', 'Wi-Fi', 'Кондиционер'],
            'is_featured' => false,
            'is_active' => true,
            'published_at' => now(),
        ]);

        Property::create([
            'title' => 'Элитный пентхаус с панорамным видом',
            'description' => 'Роскошный пентхаус на последнем этаже с потрясающим видом на город. 
                              Дизайнерский ремонт, большие окна, терраса, лифт.',
            'price' => 85000000,
            'currency' => '₽',
            'address' => 'ул. Арбат, 23',
            'city' => 'Москва',
            'area' => 220.0,
            'rooms' => 4,
            'floor' => 15,
            'total_floors' => 15,
            'type' => 'Квартира',
            'status' => 'Продается',
            'image' => 'https://via.placeholder.com/800x600/6366F1/FFFFFF?text=Luxury+Penthouse',
            'images' => [
                'https://via.placeholder.com/800x600/6366F1/FFFFFF?text=Panorama',
                'https://via.placeholder.com/800x600/6366F1/FFFFFF?text=Living+Area',
                'https://via.placeholder.com/800x600/6366F1/FFFFFF?text=Bedroom',
                'https://via.placeholder.com/800x600/6366F1/FFFFFF?text=Terrace',
            ],
            'amenities' => ['Парковка', 'Лифт', 'Консьерж', 'Охрана', 'Бассейн', 'Сауна', 'Балкон', 'Система умный дом'],
            'is_featured' => true,
            'is_active' => true,
            'published_at' => now(),
        ]);
    }
}