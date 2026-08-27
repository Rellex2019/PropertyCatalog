<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import PropertyCard from '@/Components/PropertyCard.vue';
import PropertyFilters from '@/Components/PropertyFilters.vue';
import PropertyStats from '@/Components/PropertyStats.vue';
import DynamicAuthLayout from '@/Layouts/DynamicAuthLayout.vue';

defineOptions({
    layout: DynamicAuthLayout
})

const props = defineProps({
    properties: Array,
    statistics: Object
});

const filters = ref({
    search: '',
    type: 'all',
    status: 'all',
    priceMin: '',
    priceMax: '',
    rooms: 'all'
});

const sortBy = ref('newest');

const propertyTypes = ['Квартира', 'Дом', 'Таунхаус', 'Коммерческая'];
const propertyStatuses = ['Продается', 'Сдается'];

const filteredProperties = computed(() => {
    let result = props.properties;

    if (filters.value.search) {
        const search = filters.value.search.toLowerCase();
        result = result.filter(p => 
            p.title.toLowerCase().includes(search) ||
            p.address.toLowerCase().includes(search)
        );
    }

    if (filters.value.type !== 'all') {
        result = result.filter(p => p.type === filters.value.type);
    }

    if (filters.value.status !== 'all') {
        result = result.filter(p => p.status === filters.value.status);
    }

    if (filters.value.priceMin) {
        result = result.filter(p => p.price >= Number(filters.value.priceMin));
    }
    if (filters.value.priceMax) {
        result = result.filter(p => p.price <= Number(filters.value.priceMax));
    }

    if (filters.value.rooms !== 'all') {
        result = result.filter(p => p.rooms === Number(filters.value.rooms));
    }

    switch (sortBy.value) {
        case 'price-asc':
            result = [...result].sort((a, b) => a.price - b.price);
            break;
        case 'price-desc':
            result = [...result].sort((a, b) => b.price - a.price);
            break;
        case 'newest':
            result = [...result].sort((a, b) => 
                new Date(b.created_at) - new Date(a.created_at)
            );
            break;
        default:
            break;
    }

    return result;
});

const resetFilters = () => {
    filters.value = {
        search: '',
        type: 'all',
        status: 'all',
        priceMin: '',
        priceMax: '',
        rooms: 'all'
    };
    sortBy.value = 'newest';
};

const updateFilters = () => {
    router.get('/catalog', filters.value, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <div class="py-8 px-4 sm:py-12 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    Каталог недвижимости
                </h1>
                <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">
                    {{ statistics.total }} объектов в базе
                </p>
            </div>

            <PropertyStats :statistics="statistics" />

            <PropertyFilters 
                v-model:filters="filters"
                :property-types="propertyTypes"
                :property-statuses="propertyStatuses"
                @reset="resetFilters"
                @update="updateFilters"
            />

            <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
                <p class="text-sm sm:text-base text-gray-600">
                    Найдено: <span class="font-semibold">{{ filteredProperties.length }}</span> объектов
                </p>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label class="text-sm text-gray-600 whitespace-nowrap">Сортировать:</label>
                    <select 
                        v-model="sortBy"
                        class="w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm sm:text-base"
                    >
                        <option value="newest">Сначала новые</option>
                        <option value="price-asc">Сначала дешевые</option>
                        <option value="price-desc">Сначала дорогие</option>
                    </select>
                </div>
            </div>

            <div v-if="filteredProperties.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <PropertyCard 
                    v-for="property in filteredProperties" 
                    :key="property.id"
                    :property="property"
                />
            </div>

            <div v-else class="text-center py-8 sm:py-12">
                <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Ничего не найдено</h3>
                <p class="mt-1 text-sm sm:text-base text-gray-500">Попробуйте изменить параметры поиска</p>
                <button 
                    @click="resetFilters"
                    class="mt-4 inline-flex items-center px-3 sm:px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                    Сбросить фильтры
                </button>
            </div>
        </div>
    </div>
</template>