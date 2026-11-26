<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PropertyCard from '@/Components/PropertyCard.vue';
import PropertyFilters from '@/Components/PropertyFilters.vue';
import SeoHead from '@/Components/SeoHead.vue';
import DynamicAuthLayout from '@/Layouts/DynamicAuthLayout.vue';

defineOptions({
    layout: DynamicAuthLayout
});

const props = defineProps({
    properties: Array,
    total: {
        type: Number,
        default: 0,
    },
});

const catalogDescription = computed(() =>
    `Каталог недвижимости ДомГид: ${props.total} актуальных объектов. Фильтруйте по типу, городу, цене и количеству комнат.`,
);

const catalogSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: 'Каталог недвижимости',
    url: route('catalog.index'),
    mainEntity: {
        '@type': 'ItemList',
        numberOfItems: props.total,
        itemListElement: props.properties.slice(0, 12).map((property, index) => ({
            '@type': 'ListItem',
            position: index + 1,
            name: property.title,
            url: route('catalog.show', property.id),
        })),
    },
}));

const page = usePage();

const propertyTypes = ['Квартира', 'Дом', 'Таунхаус', 'Коммерческая', 'Земельный участок'];
const propertyStatuses = ['Продается', 'Сдается'];

const initialFilters = () => {
    const params = new URLSearchParams(page.url.split('?')[1] || '');
    const type = params.get('type');
    const status = params.get('status');
    const rooms = params.get('rooms');

    return {
        search: params.get('search') || '',
        type: propertyTypes.includes(type) ? type : 'all',
        status: propertyStatuses.includes(status) ? status : 'all',
        priceMin: params.get('priceMin') || '',
        priceMax: params.get('priceMax') || '',
        rooms: ['1', '2', '3', '4', '5'].includes(rooms) ? rooms : 'all',
    };
};

const filters = ref(initialFilters());

const sortBy = ref('newest');

const backendProperties = ref([]);
const backendUsed = ref(false);
const isTyping = ref(false);
const isSearching = ref(false);

let debounceTimer = null;
let searchController = null;
let requestId = 0;

const sortProperties = (items) => {
    const result = [...items];

    switch (sortBy.value) {
        case 'price-asc':
            return result.sort((a, b) => a.price - b.price);
        case 'price-desc':
            return result.sort((a, b) => b.price - a.price);
        default:
            return result.sort((a, b) =>
                new Date(b.created_at) - new Date(a.created_at)
            );
    }
};

const filteredProperties = computed(() => {
    let result = props.properties;

    if (filters.value.search) {
        const search = filters.value.search.toLowerCase();
        result = result.filter(p =>
            p.title.toLowerCase().includes(search) ||
            p.address.toLowerCase().includes(search) ||
            (p.city || '').toLowerCase().includes(search)
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
        result = result.filter(p => Number(filters.value.rooms) === 4
            ? p.rooms >= 4
            : p.rooms === Number(filters.value.rooms));
    }

    return sortProperties(result);
});

const visibleProperties = computed(() =>
    backendUsed.value ? sortProperties(backendProperties.value) : filteredProperties.value,
);

const runBackendSearch = async () => {
    const currentRequest = ++requestId;

    if (searchController) {
        searchController.abort();
    }

    searchController = new AbortController();
    isTyping.value = false;
    isSearching.value = true;

    const params = new URLSearchParams();

    Object.entries(filters.value).forEach(([key, value]) => {
        if (value !== '' && value !== 'all') {
            params.set(key, String(value));
        }
    });

    try {
        const response = await fetch(`${route('catalog.search')}?${params.toString()}`, {
            signal: searchController.signal,
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Catalog search failed');
        }

        const data = await response.json();

        if (currentRequest === requestId) {
            backendProperties.value = data.properties || [];
            backendUsed.value = true;
        }
    } catch (error) {
        if (currentRequest === requestId && error.name !== 'AbortError') {
            backendUsed.value = false;
        }
    } finally {
        if (currentRequest === requestId) {
            isSearching.value = false;
        }
    }
};

const scheduleBackendSearch = () => {
    requestId += 1;
    clearTimeout(debounceTimer);

    if (searchController) {
        searchController.abort();
    }

    backendUsed.value = false;
    isTyping.value = filteredProperties.value.length === 0;
    isSearching.value = false;

    if (filteredProperties.value.length >= 10) {
        return;
    }

    debounceTimer = setTimeout(runBackendSearch, 1500);
};

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

watch(filters, scheduleBackendSearch, { deep: true });

onMounted(scheduleBackendSearch);

onBeforeUnmount(() => {
    requestId += 1;
    clearTimeout(debounceTimer);

    if (searchController) {
        searchController.abort();
    }
});
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
            </div>

            <div class="grid gap-8 lg:grid-cols-[17rem_minmax(0,1fr)] lg:gap-10">
                <PropertyFilters
                    v-model:filters="filters"
                    :property-types="propertyTypes"
                    :property-statuses="propertyStatuses"
                    @reset="resetFilters"
                />

                <div class="min-w-0">
                    <div class="mb-4 flex flex-col gap-3 sm:mb-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm text-brand-muted sm:text-base">
                                Найдено: <span class="font-semibold text-brand">{{ visibleProperties.length }}</span> объектов
                            </p>
                            <span v-if="isSearching && visibleProperties.length" class="inline-flex items-center gap-1.5 text-xs text-brand-soft">
                                <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                Уточняем в базе…
                            </span>
                        </div>
                        <div class="flex w-full items-center gap-2 sm:w-auto">
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

                    <div v-if="!visibleProperties.length && (isTyping || isSearching)" class="site-card px-6 py-12 text-center">
                        <svg class="mx-auto h-10 w-10 animate-spin text-brand-gold sm:h-12 sm:w-12" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <h3 class="mt-4 text-xl font-semibold text-brand">Ищем ещё объекты…</h3>
                        <p class="mt-2 text-sm text-brand-muted">
                            Показаны не все объекты — подождите, пока закончится поиск в базе.
                        </p>
                    </div>

                    <div v-else-if="visibleProperties.length" class="grid grid-cols-1 gap-6">
                        <PropertyCard
                            v-for="property in visibleProperties"
                            :key="property.id"
                            :property="property"
                            wide
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
        </div>
    </div>
</template>
