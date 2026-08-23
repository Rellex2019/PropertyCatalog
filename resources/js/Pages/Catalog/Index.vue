<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Получаем данные из глобального объекта
const page = typeof window !== 'undefined' ? window.__INERTIA_PAGE__ : null;
const layout = page?.props?.auth?.user?.name ? AuthenticatedLayout : GuestLayout;

export default {
    layout: layout
}
</script>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage,} from '@inertiajs/vue3';
import PropertyCard from '@/Components/PropertyCard.vue';
import PropertyFilters from '@/Components/PropertyFilters.vue';
import PropertyStats from '@/Components/PropertyStats.vue';

const props = defineProps({
    properties: Array,
    statistics: Object
});

// Состояние для фильтров
const filters = ref({
    search: '',
    type: 'all',
    status: 'all',
    priceMin: '',
    priceMax: '',
    rooms: 'all'
});

// Состояние для сортировки
const sortBy = ref('newest');

// Типы недвижимости
const propertyTypes = ['Квартира', 'Дом', 'Таунхаус', 'Коммерческая'];

// Статусы
const propertyStatuses = ['Продается', 'Сдается'];

// Отфильтрованные и отсортированные объекты
const filteredProperties = computed(() => {
    let result = props.properties;

    // Фильтр по поиску
    if (filters.value.search) {
        const search = filters.value.search.toLowerCase();
        result = result.filter(p => 
            p.title.toLowerCase().includes(search) ||
            p.address.toLowerCase().includes(search)
        );
    }

    // Фильтр по типу
    if (filters.value.type !== 'all') {
        result = result.filter(p => p.type === filters.value.type);
    }

    // Фильтр по статусу
    if (filters.value.status !== 'all') {
        result = result.filter(p => p.status === filters.value.status);
    }

    // Фильтр по ценам
    if (filters.value.priceMin) {
        result = result.filter(p => p.price >= Number(filters.value.priceMin));
    }
    if (filters.value.priceMax) {
        result = result.filter(p => p.price <= Number(filters.value.priceMax));
    }

    // Фильтр по комнатам
    if (filters.value.rooms !== 'all') {
        result = result.filter(p => p.rooms === Number(filters.value.rooms));
    }

    // Сортировка
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

// Сброс фильтров
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

// Обновление URL с фильтрами (для сохранения состояния)
const updateFilters = () => {
    router.get('/catalog', filters.value, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Заголовок -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    Каталог недвижимости
                </h1>
                <p class="mt-2 text-gray-600">
                    {{ statistics.total }} объектов в базе
                </p>
            </div>

            <!-- Статистика -->
            <PropertyStats :statistics="statistics" />

            <!-- Фильтры -->
            <PropertyFilters 
                v-model:filters="filters"
                :property-types="propertyTypes"
                :property-statuses="propertyStatuses"
                @reset="resetFilters"
                @update="updateFilters"
            />

            <!-- Сортировка -->
            <div class="mb-6 flex justify-between items-center">
                <p class="text-gray-600">
                    Найдено: <span class="font-semibold">{{ filteredProperties.length }}</span> объектов
                </p>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Сортировать:</label>
                    <select 
                        v-model="sortBy"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                        <option value="newest">Сначала новые</option>
                        <option value="price-asc">Сначала дешевые</option>
                        <option value="price-desc">Сначала дорогие</option>
                    </select>
                </div>
            </div>

            <!-- Сетка карточек -->
            <div v-if="filteredProperties.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <PropertyCard 
                    v-for="property in filteredProperties" 
                    :key="property.id"
                    :property="property"
                />
            </div>

            <!-- Сообщение, если объектов нет -->
            <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Ничего не найдено</h3>
                <p class="mt-1 text-gray-500">Попробуйте изменить параметры поиска</p>
                <button 
                    @click="resetFilters"
                    class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                    Сбросить фильтры
                </button>
            </div>
        </div>
    </div>
</template>