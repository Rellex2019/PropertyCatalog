<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout
});

const page = usePage();

const stats = computed(() => [
    { 
        name: 'Можно вывести различные данные сюда', 
        value: page.props.statistics?.total || 0,
        icon: '📊',
        bgColor: 'bg-blue-100',
        textColor: 'text-blue-600'
    },
]);
</script>

<template>
    <div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Дашборд</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div 
                v-for="stat in stats" 
                :key="stat.name"
                class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300"
            >
                <div class="p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center text-xl sm:text-2xl" :class="stat.bgColor">
                                {{ stat.icon }}
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <p class="text-xs sm:text-sm font-medium text-gray-500">
                                {{ stat.name }}
                            </p>
                            <p class="text-xl sm:text-2xl font-semibold text-gray-900">
                                {{ stat.value }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>