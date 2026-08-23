<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    property: {
        type: Object,
        required: true
    }
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('ru-RU').format(price);
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
        <Link :href="route('catalog.show', property.id)" class="block">
            <!-- Изображение -->
            <div class="relative h-48 overflow-hidden">
                <img 
                    :src="property.image" 
                    :alt="property.title"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                >
                
                <span class="absolute top-4 left-4 px-3 py-1 bg-indigo-600 text-white text-xs font-semibold rounded-full">
                    {{ property.type }}
                </span>
                
                <span class="absolute top-4 right-4 px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">
                    {{ property.status }}
                </span>
                
                <span 
                    v-if="property.is_featured"
                    class="absolute bottom-4 left-4 px-3 py-1 bg-yellow-500 text-white text-xs font-semibold rounded-full"
                >
                    ★ Избранное
                </span>
            </div>

            <!-- Контент -->
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
                    {{ property.title }}
                </h3>

                <p class="mt-1 text-sm text-gray-500 flex items-start gap-1">
                    <svg class="h-4 w-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ property.address }}
                </p>

                <div class="mt-3 grid grid-cols-3 gap-2 py-3 border-t border-b border-gray-100">
                    <div class="text-center">
                        <span class="block text-sm font-medium text-gray-900">{{ property.area }}</span>
                        <span class="text-xs text-gray-500">м²</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-sm font-medium text-gray-900">{{ property.rooms }}</span>
                        <span class="text-xs text-gray-500">комнат</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-sm font-medium text-gray-900">{{ property.floor }}</span>
                        <span class="text-xs text-gray-500">этаж</span>
                    </div>
                </div>

                <div class="mt-3">
                    <span class="text-xl font-bold text-indigo-600">{{ formatPrice(property.price) }}</span>
                    <span class="text-sm text-gray-500"> {{ property.currency }}</span>
                </div>
            </div>
        </Link>
    </div>
</template>