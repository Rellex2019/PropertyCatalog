<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout
});

const props = defineProps({
    properties: Object,
    statistics: Object,
    filters: Object,
    sort: Object
});

const search = ref(props.filters?.search || '');

const formatPrice = (price) => {
    return new Intl.NumberFormat('ru-RU').format(price);
};

const getStatusBadge = (status) => {
    const statusMap = {
        'Продается': 'bg-green-100 text-green-800',
        'Сдается': 'bg-blue-100 text-blue-800',
        'Продано': 'bg-red-100 text-red-800'
    };
    return statusMap[status] || 'bg-gray-100 text-gray-800';
};

const performSearch = () => {
    router.get('/admin/properties', { 
        ...props.filters, 
        search: search.value 
    }, {
        preserveState: true
    });
};

const deleteProperty = (property) => {
    if (confirm(`Вы уверены, что хотите удалить "${property.title}"?`)) {
        router.delete(`/admin/properties/${property.id}`);
    }
};
</script>

<template>
    <div>
        <h1>
            <div class="flex justify-between items-center w-full">
                <span>Управление недвижимостью</span>
                <Link 
                    :href="route('admin.properties.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                    + Добавить объект
                </Link>
            </div>
        </h1>

        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Всего</p>
                <p class="text-2xl font-bold">{{ statistics?.total || 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Активных</p>
                <p class="text-2xl font-bold text-green-600">{{ statistics?.active || 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">В избранном</p>
                <p class="text-2xl font-bold text-yellow-600">{{ statistics?.featured || 0 }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Типов</p>
                <p class="text-2xl font-bold">{{ statistics?.types?.length || 0 }}</p>
            </div>
        </div>

        <!-- Поиск -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex gap-4">
                <input 
                    v-model="search"
                    type="text"
                    placeholder="Поиск по названию или адресу..."
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    @keyup.enter="performSearch"
                >
                <button 
                    @click="performSearch"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    Найти
                </button>
            </div>
        </div>

        <!-- Таблица -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Изображение</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Цена</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="property in properties.data" :key="property.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ property.id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img 
                                :src="property.image || 'https://via.placeholder.com/50x50/4F46E5/FFFFFF?text=No+Image'" 
                                :alt="property.title"
                                class="h-12 w-12 object-cover rounded"
                            >
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ property.title }}</div>
                            <div class="text-sm text-gray-500">{{ property.address }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ formatPrice(property.price) }} ₽
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ property.type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="['px-2 py-1 text-xs font-semibold rounded-full', getStatusBadge(property.status)]">
                                {{ property.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link 
                                :href="`/admin/properties/${property.id}/edit`"
                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                            >
                                Редактировать
                            </Link>
                            <button 
                                @click="deleteProperty(property)"
                                class="text-red-600 hover:text-red-900"
                            >
                                Удалить
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Пагинация -->
            <div v-if="properties.links" class="px-6 py-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Показано {{ properties.from }} - {{ properties.to }} из {{ properties.total }}
                    </div>
                    <div class="flex gap-2">
                        <Link 
                            v-for="link in properties.links" 
                            :key="link.label"
                            :href="link.url || '#'"
                            class="px-3 py-1 rounded text-sm"
                            :class="[
                                link.active ? 'bg-indigo-600 text-white' : 'bg-gray-100 hover:bg-gray-200',
                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>