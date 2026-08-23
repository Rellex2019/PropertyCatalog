<script setup>
import { ref, defineProps, defineEmits } from 'vue';

const props = defineProps({
    filters: Object,
    propertyTypes: Array,
    propertyStatuses: Array
});

const emit = defineEmits(['update:filters', 'reset', 'update']);

const isFilterOpen = ref(false);

// Применение фильтров
const applyFilters = () => {
    emit('update');
};

// Сброс фильтров
const resetFilters = () => {
    emit('reset');
    emit('update');
};
</script>

<template>
    <div class="mb-6">
        <!-- Кнопка показа/скрытия фильтров -->
        <button 
            @click="isFilterOpen = !isFilterOpen"
            class="w-full md:w-auto px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center gap-2"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            {{ isFilterOpen ? 'Скрыть фильтры' : 'Показать фильтры' }}
            <span class="text-xs text-gray-500">({{ Object.values(filters).filter(v => v && v !== 'all').length }})</span>
        </button>

        <!-- Панель фильтров -->
        <div v-show="isFilterOpen" class="mt-4 p-4 bg-white rounded-lg shadow-md border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Поиск -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                    <input 
                        v-model="filters.search"
                        type="text"
                        placeholder="Название или адрес..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        @input="applyFilters"
                    >
                </div>

                <!-- Тип недвижимости -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Тип</label>
                    <select 
                        v-model="filters.type"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        @change="applyFilters"
                    >
                        <option value="all">Все типы</option>
                        <option v-for="type in propertyTypes" :key="type" :value="type">
                            {{ type }}
                        </option>
                    </select>
                </div>

                <!-- Статус -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                    <select 
                        v-model="filters.status"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        @change="applyFilters"
                    >
                        <option value="all">Все статусы</option>
                        <option v-for="status in propertyStatuses" :key="status" :value="status">
                            {{ status }}
                        </option>
                    </select>
                </div>

                <!-- Цена от -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Цена от (₽)</label>
                    <input 
                        v-model="filters.priceMin"
                        type="number"
                        placeholder="Минимальная"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        @input="applyFilters"
                    >
                </div>

                <!-- Цена до -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Цена до (₽)</label>
                    <input 
                        v-model="filters.priceMax"
                        type="number"
                        placeholder="Максимальная"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        @input="applyFilters"
                    >
                </div>

                <!-- Количество комнат -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Комнат</label>
                    <select 
                        v-model="filters.rooms"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        @change="applyFilters"
                    >
                        <option value="all">Любое</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4+</option>
                    </select>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="mt-4 flex justify-end gap-2">
                <button 
                    @click="resetFilters"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900"
                >
                    Сбросить все
                </button>
            </div>
        </div>
    </div>
</template>