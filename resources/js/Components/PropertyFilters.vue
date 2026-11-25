<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    filters: Object,
    propertyTypes: Array,
    propertyStatuses: Array,
});

const emit = defineEmits(['reset']);
const isFilterOpen = ref(false);

const openFilters = () => {
    isFilterOpen.value = true;
};

const closeFilters = () => {
    isFilterOpen.value = false;
};

const resetFilters = () => {
    emit('reset');
    closeFilters();
};

const typeOptions = computed(() => [
    { value: 'all', label: 'Все' },
    ...props.propertyTypes.map((type) => ({ value: type, label: type })),
]);

const statusOptions = computed(() => [
    { value: 'all', label: 'Все' },
    ...props.propertyStatuses.map((status) => ({ value: status, label: status })),
]);

const roomOptions = [
    { value: 'all', label: 'Все' },
    { value: '1', label: '1' },
    { value: '2', label: '2' },
    { value: '3', label: '3' },
    { value: '4', label: '4+' },
];

const activeCount = computed(() => Object.values(props.filters).filter((value) => value && value !== 'all').length);

const activeFilters = computed(() => {
    const items = [];

    if (props.filters.search) {
        items.push(`Поиск: ${props.filters.search}`);
    }
    if (props.filters.type !== 'all') {
        items.push(props.filters.type);
    }
    if (props.filters.status !== 'all') {
        items.push(props.filters.status);
    }
    if (props.filters.priceMin) {
        items.push(`от ${props.filters.priceMin} ₽`);
    }
    if (props.filters.priceMax) {
        items.push(`до ${props.filters.priceMax} ₽`);
    }
    if (props.filters.rooms !== 'all') {
        items.push(`${props.filters.rooms === '4' ? '4+' : props.filters.rooms} комн.`);
    }

    return items;
});

const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        closeFilters();
    }
};

const handleResize = () => {
    if (window.innerWidth >= 1024) {
        closeFilters();
    }
};

watch(isFilterOpen, (open) => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = open ? 'hidden' : '';
    }
});

onMounted(() => {
    if (typeof window !== 'undefined') {
        window.addEventListener('keydown', handleKeydown);
        window.addEventListener('resize', handleResize);
    }
});

onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', handleKeydown);
        window.removeEventListener('resize', handleResize);
        document.body.style.overflow = '';
    }
});
</script>

<template>
    <aside class="lg:sticky lg:top-24 lg:self-start">
        <button
            v-if="!isFilterOpen"
            type="button"
            class="fixed left-0 top-24 z-30 flex flex-col items-center gap-2 rounded-r-2xl border border-l-0 border-brand-line bg-white px-3 py-4 shadow-lg transition hover:bg-brand-cream lg:hidden"
            aria-label="Открыть фильтры"
            @click="openFilters"
        >
            <svg class="h-5 w-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span class="text-[10px] font-semibold text-brand">Фильтры</span>
            <span class="rounded-full bg-brand-sand px-2 py-0.5 text-[10px] text-brand">{{ activeCount }}</span>
        </button>

        <div
            v-if="isFilterOpen"
            class="fixed inset-0 z-40 bg-brand/60 backdrop-blur-sm lg:hidden"
            @click="closeFilters"
        ></div>

        <div
            :class="isFilterOpen
                ? 'fixed inset-y-0 left-0 z-50 flex w-[88vw] max-w-sm flex-col overflow-y-auto rounded-none border-r border-brand-line shadow-2xl lg:static lg:z-auto lg:w-auto lg:max-w-none lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto lg:rounded-2xl'
                : 'hidden flex-col lg:flex lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto'"
            :role="isFilterOpen ? 'dialog' : undefined"
            :aria-modal="isFilterOpen ? 'true' : undefined"
            class="site-card overflow-hidden border-brand-line p-0 shadow-lg"
        >
            <div class="site-filter-header flex items-start justify-between gap-3 p-5">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-brand text-white shadow-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-brand">Подбор объектов</h2>
                    </div>
                </div>
                <button
                    type="button"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-xl text-brand-soft transition hover:bg-brand-sand hover:text-brand lg:hidden"
                    aria-label="Закрыть фильтры"
                    @click="closeFilters"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-5 sm:p-6">
                <div v-if="activeFilters.length" class="mb-5 flex flex-wrap gap-2">
                    <span
                        v-for="(item, index) in activeFilters"
                        :key="index"
                        class="site-badge bg-brand-sand text-brand"
                    >
                        {{ item }}
                    </span>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="site-label" for="filter-search">Поиск</label>
                        <div class="relative mt-1">
                            <svg class="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-brand" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" />
                                <path stroke-linecap="round" d="M20 20l-3.6-3.6" />
                            </svg>
                            <input
                                id="filter-search"
                                v-model="filters.search"
                                type="text"
                                placeholder="Название или адрес"
                                class="site-input pl-12"
                            />
                        </div>
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.12em] text-brand-soft">Тип объекта</p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="option in typeOptions"
                                :key="option.value"
                                type="button"
                                class="rounded-full border px-3 py-2 text-xs font-semibold transition"
                                :class="filters.type === option.value
                                    ? 'border-brand bg-brand text-white shadow-sm'
                                    : 'border-brand-line bg-white text-brand-muted hover:border-brand-gold hover:text-brand'"
                                @click="filters.type = option.value"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.12em] text-brand-soft">Статус</p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="option in statusOptions"
                                :key="option.value"
                                type="button"
                                class="rounded-full border px-3 py-2 text-xs font-semibold transition"
                                :class="filters.status === option.value
                                    ? 'border-brand-forest bg-brand-forest text-white shadow-sm'
                                    : 'border-brand-line bg-white text-brand-muted hover:border-brand-gold hover:text-brand'"
                                @click="filters.status = option.value"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.12em] text-brand-soft">Цена</p>
                        <div class="space-y-3">
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-brand-soft">₽</span>
                                <input
                                    v-model="filters.priceMin"
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    placeholder="От"
                                    aria-label="Цена от"
                                    class="site-input pl-9"
                                />
                            </div>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-brand-soft">₽</span>
                                <input
                                    v-model="filters.priceMax"
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    placeholder="До"
                                    aria-label="Цена до"
                                    class="site-input pl-9"
                                />
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.12em] text-brand-soft">Комнаты</p>
                        <div class="grid grid-cols-5 gap-2">
                            <button
                                v-for="option in roomOptions"
                                :key="option.value"
                                type="button"
                                class="rounded-xl border px-2 py-2.5 text-xs font-semibold transition"
                                :class="filters.rooms === option.value
                                    ? 'border-brand-gold bg-brand-gold text-brand shadow-sm'
                                    : 'border-brand-line bg-white text-brand-muted hover:border-brand-gold hover:text-brand'"
                                @click="filters.rooms = option.value"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t border-brand-line pt-5">
                    <button type="button" class="site-link" @click="resetFilters">Сбросить всё</button>
                </div>
            </div>
        </div>
    </aside>
</template>
