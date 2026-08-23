<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'currency',
        'address',
        'city',
        'area',
        'rooms',
        'floor',
        'total_floors',
        'type',
        'status',
        'image',
        'images',
        'amenities',
        'is_featured',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area' => 'decimal:2',
        'images' => 'array',
        'amenities' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Аксессор для форматирования цены
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, '.', ' ') . ' ' . $this->currency;
    }

    // Аксессор для получения главного изображения
    public function getMainImageAttribute(): string
    {
        return $this->image ?? 'https://via.placeholder.com/800x600/4F46E5/FFFFFF?text=No+Image';
    }

    // Аксессор для всех изображений
    public function getAllImagesAttribute(): array
    {
        $images = $this->images ?? [];
        if ($this->image) {
            array_unshift($images, $this->image);
        }
        return $images;
    }

    // Scope для активных объектов
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope для избранных
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope по типу
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope по статусу
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}