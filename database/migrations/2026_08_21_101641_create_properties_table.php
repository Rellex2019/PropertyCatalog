<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('currency', 3)->default('₽');
            $table->string('address');
            $table->string('city')->nullable();
            $table->decimal('area', 10, 2);
            $table->integer('rooms');
            $table->integer('floor');
            $table->integer('total_floors');
            $table->string('type'); // Квартира, Дом, Таунхаус и т.д.
            $table->string('status')->default('Продается'); // Продается, Сдается, Продано
            $table->string('image')->nullable();
            $table->json('images')->nullable(); // Дополнительные фото
            $table->json('amenities')->nullable(); // Удобства
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('price');
            $table->index('rooms');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
