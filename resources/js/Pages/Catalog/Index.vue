<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PropertyCard from '@/Components/PropertyCard.vue';
import PropertyFilters from '@/Components/PropertyFilters.vue';
import PropertyStats from '@/Components/PropertyStats.vue';
import SeoHead from '@/Components/SeoHead.vue';
import DynamicAuthLayout from '@/Layouts/DynamicAuthLayout.vue';

defineOptions({
    layout: DynamicAuthLayout
});

const props = defineProps({
    properties: Array,
    statistics: Object
});

const catalogDescription = computed(() =>
    `Каталог недвижимости ДомГид: ${props.properties.length} актуальных объектов. Фильтруйте по типу, городу, цене и количеству комнат.`,
);

const catalogSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: 'Каталог недвижимости',
    url: route('catalog.index'),
    mainEntity: {
        '@type': 'ItemList',
        numberOfItems: props.properties.length,
        itemListElement: props.properties.slice(0, 12).map((property, index) => ({
            '@type': 'ListItem',
            position: index + 1,
            name: property.title,
            url: route('catalog.show', property.id),
        })),
    },
}));

// Состояние для фильтров
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
    <SeoHead
        title="Каталог недвижимости"
        :description="catalogDescription"
        :canonical="route('catalog.index')"
        :schema="catalogSchema"
    />

    <div class="py-8 sm:py-12">
        <div class="site-container">
            <div class="mb-8">
                <p class="site-eyebrow">Недвижимость</p>
                <h1 class="site-title mt-3">
                    Каталог недвижимости
                </h1>
                <p class="mt-3 text-sm text-brand-muted sm:text-base">
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
                <p class="text-sm text-brand-muted sm:text-base">
                    Найдено: <span class="font-semibold text-brand">{{ filteredProperties.length }}</span> объектов
                </p>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label class="whitespace-nowrap text-sm text-brand-muted">Сортировать:</label>
                    <select
                        v-model="sortBy"
                        class="site-input w-full sm:w-auto"
                    >
                        <option value="newest">Сначала новые</option>
                        <option value="price-asc">Сначала дешевые</option>
                        <option value="price-desc">Сначала дорогие</option>
                    </select>
                </div>
            </div>

            <div v-if="filteredProperties.length" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 sm:gap-8">
                <PropertyCard 
                    v-for="property in filteredProperties" 
                    :key="property.id"
                    :property="property"
                />
            </div>

            <div v-else class="site-card px-6 py-12 text-center">
                <svg class="mx-auto h-10 w-10 text-brand-soft sm:h-12 sm:w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-xl font-semibold text-brand">Ничего не найдено</h3>
                <p class="mt-2 text-sm text-brand-muted">Попробуйте изменить параметры поиска</p>
                <button
                    type="button"
                    class="site-btn-primary mt-6"
                    @click="resetFilters"
                >
                    Сбросить фильтры
                </button>
            </div>
        </div>
    </div>
</template>