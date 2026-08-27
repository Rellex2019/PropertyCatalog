<template>
    <div>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Добавление объекта</h1>
            <p class="text-gray-600">Создание нового объекта недвижимости</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500">Форма добавления объекта (в разработке)</p>
            
            <div class="mt-4 flex gap-4">
                <Link 
                    href="/admin/properties"
                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                >
                    Назад к списку
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout
});
</script>