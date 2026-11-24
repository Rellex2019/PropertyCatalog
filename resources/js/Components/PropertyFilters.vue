<script setup>
import { ref } from 'vue';

const props = defineProps({
    filters: Object,
    propertyTypes: Array,
    propertyStatuses: Array,
});

const emit = defineEmits(['update:filters', 'reset', 'update']);
const isFilterOpen = ref(false);

const applyFilters = () => emit('update');

const resetFilters = () => {
    emit('reset');
    emit('update');
};

const activeCount = () => Object.values(props.filters).filter((value) => value && value !== 'all').length;
</script>

<template>
    <div class="mb-8">
        <button
            type="button"
            class="site-btn-outline w-full md:w-auto"
            @click="isFilterOpen = !isFilterOpen"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            {{ isFilterOpen ? 'Скрыть фильтры' : 'Показать фильтры' }}
            <span class="text-xs text-brand-soft">({{ activeCount() }})</span>
        </button>

        <div v-show="isFilterOpen" class="site-card mt-4 p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="site-label">Поиск</label>
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Название или адрес..."
                        class="site-input"
                        @input="applyFilters"
                    />
                </div>
                <div>
                    <label class="site-label">Тип</label>
                    <select v-model="filters.type" class="site-input" @change="applyFilters">
                        <option value="all">Все типы</option>
                        <option v-for="type in propertyTypes" :key="type" :value="type">{{ type }}</option>
                    </select>
                </div>
                <div>
                    <label class="site-label">Статус</label>
                    <select v-model="filters.status" class="site-input" @change="applyFilters">
                        <option value="all">Все статусы</option>
                        <option v-for="status in propertyStatuses" :key="status" :value="status">{{ status }}</option>
                    </select>
                </div>
                <div>
                    <label class="site-label">Цена от (₽)</label>
                    <input
                        v-model="filters.priceMin"
                        type="number"
                        placeholder="Минимальная"
                        class="site-input"
                        @input="applyFilters"
                    />
                </div>
                <div>
                    <label class="site-label">Цена до (₽)</label>
                    <input
                        v-model="filters.priceMax"
                        type="number"
                        placeholder="Максимальная"
                        class="site-input"
                        @input="applyFilters"
                    />
                </div>
                <div>
                    <label class="site-label">Комнат</label>
                    <select v-model="filters.rooms" class="site-input" @change="applyFilters">
                        <option value="all">Любое</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4+</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" class="site-link" @click="resetFilters">Сбросить все</button>
            </div>
        </div>
    </div>
</template>
