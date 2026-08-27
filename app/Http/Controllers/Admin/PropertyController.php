<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller 
{
    /**
     * Список объектов недвижимости
     */
    public function index(Request $request): Response
    {
        $query = Property::query();

        // Поиск
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Фильтр по типу
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Фильтр по статусу
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Сортировка
        $sortField = $request->sort_field ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        $properties = $query->paginate(15)
            ->withQueryString();

        // Статистика
        $statistics = [
            'total' => Property::count(),
            'active' => Property::where('is_active', true)->count(),
            'featured' => Property::where('is_featured', true)->count(),
            'types' => Property::select('type')->distinct()->pluck('type'),
            'statuses' => ['Продается', 'Сдается', 'Продано'],
        ];

        return Inertia::render('Admin/Properties/Index', [
            'properties' => $properties,
            'statistics' => $statistics,
            'filters' => $request->only(['search', 'type', 'status']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Форма создания объекта
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Properties/Create', [
            'types' => ['Квартира', 'Дом', 'Таунхаус', 'Коммерческая', 'Земельный участок'],
            'statuses' => ['Продается', 'Сдается', 'Продано'],
            'amenitiesList' => [
                'Парковка', 'Лифт', 'Консьерж', 'Охрана', 'Детская площадка',
                'Спортзал', 'Бассейн', 'Сауна', 'Wi-Fi', 'Кондиционер',
                'Балкон', 'Терраса', 'Камин', 'Система умный дом',
                'Мебель', 'Бытовая техника', 'Интернет', 'Кабельное ТВ'
            ],
        ]);
    }

    /**
     * Сохранение нового объекта
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'area' => 'required|numeric|min:0',
            'rooms' => 'required|integer|min:0',
            'floor' => 'required|integer|min:0',
            'total_floors' => 'required|integer|min:0',
            'type' => 'required|string',
            'status' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'amenities' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Обработка главного изображения
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('properties', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        // Обработка дополнительных изображений
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('properties', 'public');
                $images[] = '/storage/' . $path;
            }
            $validated['images'] = $images;
        }

        // Преобразование amenities в JSON
        if (isset($validated['amenities'])) {
            $validated['amenities'] = array_values($validated['amenities']);
        }

        // Установка даты публикации
        if (empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        Property::create($validated);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Объект успешно создан!');
    }

    /**
     * Редактирование объекта
     */
    public function edit(Property $property): Response
    {
        return Inertia::render('Admin/Properties/Edit', [
            'property' => $property,
            'types' => ['Квартира', 'Дом', 'Таунхаус', 'Коммерческая', 'Земельный участок'],
            'statuses' => ['Продается', 'Сдается', 'Продано'],
            'amenitiesList' => [
                'Парковка', 'Лифт', 'Консьерж', 'Охрана', 'Детская площадка',
                'Спортзал', 'Бассейн', 'Сауна', 'Wi-Fi', 'Кондиционер',
                'Балкон', 'Терраса', 'Камин', 'Система умный дом',
                'Мебель', 'Бытовая техника', 'Интернет', 'Кабельное ТВ'
            ],
        ]);
    }

    /**
     * Обновление объекта
     */
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'area' => 'required|numeric|min:0',
            'rooms' => 'required|integer|min:0',
            'floor' => 'required|integer|min:0',
            'total_floors' => 'required|integer|min:0',
            'type' => 'required|string',
            'status' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'amenities' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Обработка главного изображения
        if ($request->hasFile('image')) {
            // Удаляем старое изображение
            if ($property->image) {
                $oldPath = str_replace('/storage/', '', $property->image);
                Storage::disk('public')->delete($oldPath);
            }
            
            $image = $request->file('image');
            $path = $image->store('properties', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        // Обработка дополнительных изображений
        if ($request->hasFile('images')) {
            // Удаляем старые изображения
            if ($property->images) {
                foreach ($property->images as $oldImage) {
                    $oldPath = str_replace('/storage/', '', $oldImage);
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('properties', 'public');
                $images[] = '/storage/' . $path;
            }
            $validated['images'] = $images;
        }

        // Преобразование amenities в JSON
        if (isset($validated['amenities'])) {
            $validated['amenities'] = array_values($validated['amenities']);
        }

        $property->update($validated);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Объект успешно обновлен!');
    }

    /**
     * Удаление объекта
     */
    public function destroy(Property $property)
    {
        // Удаляем изображения
        if ($property->image) {
            $path = str_replace('/storage/', '', $property->image);
            Storage::disk('public')->delete($path);
        }
        
        if ($property->images) {
            foreach ($property->images as $image) {
                $path = str_replace('/storage/', '', $image);
                Storage::disk('public')->delete($path);
            }
        }

        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Объект успешно удален!');
    }

    /**
     * Публикация/Снятие с публикации
     */
    public function toggleActive(Property $property)
    {
        $property->update([
            'is_active' => !$property->is_active,
            'published_at' => $property->is_active ? null : now(),
        ]);

        $status = $property->is_active ? 'опубликован' : 'снят с публикации';
        return back()->with('success', "Объект успешно {$status}!");
    }

    /**
     * Переключение избранного
     */
    public function toggleFeatured(Property $property)
    {
        $property->update([
            'is_featured' => !$property->is_featured,
        ]);

        $status = $property->is_featured ? 'добавлен в избранное' : 'удален из избранного';
        return back()->with('success', "Объект успешно {$status}!");
    }
}