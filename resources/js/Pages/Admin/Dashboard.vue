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
    // { 
    //     name: 'Активных', 
    //     value: page.props.statistics?.active || 0,
    //     icon: '✅',
    //     bgColor: 'bg-green-100',
    //     textColor: 'text-green-600'
    // },
    // { 
    //     name: 'В избранном', 
    //     value: page.props.statistics?.featured || 0,
    //     icon: '⭐',
    //     bgColor: 'bg-yellow-100',
    //     textColor: 'text-yellow-600'
    // },
    // { 
    //     name: 'Типов объектов', 
    //     value: page.props.statistics?.types?.length || 0,
    //     icon: '🏠',
    //     bgColor: 'bg-purple-100',
    //     textColor: 'text-purple-600'
    // },
]);
</script>

<template>
    <div>
        <!-- Убираем <template #header> и просто используем заголовок -->
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Дашборд</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div 
                v-for="stat in stats" 
                :key="stat.name"
                class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300"
            >
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl" :class="stat.bgColor">
                                {{ stat.icon }}
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">
                                {{ stat.name }}
                            </p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ stat.value }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>