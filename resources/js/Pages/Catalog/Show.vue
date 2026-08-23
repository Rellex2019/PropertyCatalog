<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineOptions({
    layout: AuthenticatedLayout
});

const props = defineProps({
    property: Object,
    similarProperties: Array,
    propertyUrl: String
});

// Состояние для QR-кода
const isQrLoading = ref(false);
const qrCode = ref(null);
const isQrUnlocked = ref(false);

// Главное изображение
const mainImage = computed(() => {
    return props.property.main_image || props.property.image || 
           'https://via.placeholder.com/800x600/4F46E5/FFFFFF?text=No+Image';
});

// Все изображения
const images = computed(() => {
    return props.property.all_images || [];
});

// Форматирование цены
const formattedPrice = computed(() => {
    return new Intl.NumberFormat('ru-RU').format(props.property.price);
});

// Текущее изображение для показа
const currentImage = ref(mainImage.value);

// Смена изображения
const changeImage = (image) => {
    currentImage.value = image;
};

// Генерация QR-кода при нажатии
const generateQR = async () => {
    if (qrCode.value) {
        isQrUnlocked.value = true;
        return;
    }

    isQrLoading.value = true;

    try {
        const url = props.propertyUrl;
        
        console.log('URL для QR:', url);

        const response = await fetch('/qr/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ data: url })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success && result.qrCode) {
            qrCode.value = result.qrCode;
            isQrUnlocked.value = true;
        } else {
            throw new Error(result.message || 'Не удалось сгенерировать QR-код');
        }
    } catch (error) {
        console.error('Ошибка генерации QR:', error);
        alert('Не удалось сгенерировать QR-код. Попробуйте позже.');
    } finally {
        isQrLoading.value = false;
    }
};

// Сброс QR (скрыть)
const resetQR = () => {
    isQrUnlocked.value = false;
};

// Удобства
const amenities = computed(() => {
    return props.property.amenities || [];
});

// Метка статуса
const statusClass = computed(() => {
    const statusMap = {
        'Продается': 'bg-green-500',
        'Сдается': 'bg-blue-500',
        'Продано': 'bg-red-500'
    };
    return statusMap[props.property.status] || 'bg-gray-500';
});
</script>

<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Хлебные крошки -->
            <nav class="flex mb-4 text-sm text-gray-500">
                <Link href="/" class="hover:text-gray-700">Главная</Link>
                <span class="mx-2">/</span>
                <Link href="/catalog" class="hover:text-gray-700">Каталог</Link>
                <span class="mx-2">/</span>
                <span class="text-gray-900">{{ property.title }}</span>
            </nav>

            <!-- Основная информация -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Галерея изображений -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                    <!-- Главное изображение -->
                    <div class="relative">
                        <img 
                            :src="currentImage" 
                            :alt="property.title"
                            class="w-full h-96 object-cover rounded-lg"
                        />
                        
                        <!-- Бейджи -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            <span class="px-3 py-1 bg-indigo-600 text-white text-xs font-semibold rounded-full">
                                {{ property.type }}
                            </span>
                            <span 
                                v-if="property.is_featured"
                                class="px-3 py-1 bg-yellow-500 text-white text-xs font-semibold rounded-full"
                            >
                                ★ Избранное
                            </span>
                        </div>
                        
                        <span 
                            :class="['absolute top-4 right-4 px-3 py-1 text-white text-xs font-semibold rounded-full', statusClass]"
                        >
                            {{ property.status }}
                        </span>
                    </div>

                    <!-- Информация -->
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            {{ property.title }}
                        </h1>
                        
                        <p class="text-gray-600 mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ property.address }}, {{ property.city }}
                        </p>

                        <div class="flex items-center mb-6">
                            <span class="text-3xl font-bold text-indigo-600">
                                {{ formattedPrice }} ₽
                            </span>
                        </div>

                        <!-- Характеристики -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <span class="block text-sm text-gray-500">Площадь</span>
                                <span class="text-lg font-semibold">{{ property.area }} м²</span>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <span class="block text-sm text-gray-500">Комнат</span>
                                <span class="text-lg font-semibold">{{ property.rooms }}</span>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <span class="block text-sm text-gray-500">Этаж</span>
                                <span class="text-lg font-semibold">{{ property.floor }} / {{ property.total_floors }}</span>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <span class="block text-sm text-gray-500">Дата публикации</span>
                                <span class="text-lg font-semibold">{{ new Date(property.published_at).toLocaleDateString('ru-RU') }}</span>
                            </div>
                        </div>

                        <!-- Удобства -->
                        <div v-if="amenities.length" class="mb-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Удобства:</h3>
                            <div class="flex flex-wrap gap-2">
                                <span 
                                    v-for="amenity in amenities" 
                                    :key="amenity"
                                    class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs rounded-full"
                                >
                                    {{ amenity }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Описание -->
                <div class="p-6 border-t border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Описание</h2>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ property.description }}
                    </p>
                </div>

                <!-- QR-код -->
                <div class="p-6 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">QR-код объекта</h2>
                        <button
                            v-if="isQrUnlocked"
                            @click="resetQR"
                            class="text-sm text-gray-500 hover:text-gray-700"
                        >
                            Скрыть QR-код
                        </button>
                    </div>

                    <div class="flex flex-col items-center">
                        <!-- Замкнутый QR (размытый) -->
                        <div 
                            v-if="!isQrUnlocked"
                            @click="generateQR"
                            class="relative cursor-pointer group"
                        >
                            <div class="w-48 h-48 bg-gray-200 rounded-lg flex items-center justify-center relative overflow-hidden">
                                <!-- Заглушка вместо размытого QR -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </div>
                                <!-- Размытый слой (имитация) -->
                                <div class="absolute inset-0 bg-gray-300/50 backdrop-blur-sm"></div>
                                
                                <!-- Замок -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="bg-white/90 p-3 rounded-full shadow-lg">
                                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Подсказка при наведении -->
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                    <span class="text-white font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                                        Нажмите, чтобы разблокировать
                                    </span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500 text-center">
                                Нажмите для генерации QR-кода
                            </p>
                        </div>

                        <!-- Разблокированный QR -->
                        <div v-else class="flex flex-col items-center">
                            <div class="w-48 h-48 relative">
                                <img 
                                    v-if="qrCode" 
                                    :src="qrCode" 
                                    alt="QR Code"
                                    class="w-full h-full object-contain rounded-lg"
                                />
                                <div v-else-if="isQrLoading" class="w-full h-full flex items-center justify-center bg-gray-100 rounded-lg">
                                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500 text-center">
                                QR-код для просмотра объекта
                            </p>
                            <button 
                                @click="generateQR"
                                class="mt-3 px-4 py-2 text-sm text-indigo-600 hover:text-indigo-800"
                            >
                                Обновить QR-код
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Похожие объекты -->
            <div v-if="similarProperties.length" class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Похожие объекты</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div 
                        v-for="similarProperty in similarProperties" 
                        :key="similarProperty.id"
                        class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow"
                    >
                        <Link :href="route('catalog.show', similarProperty.id)">
                            <img 
                                :src="similarProperty.image" 
                                :alt="similarProperty.title"
                                class="w-full h-48 object-cover"
                            />
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    {{ similarProperty.title }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-2">{{ similarProperty.address }}</p>
                                <p class="text-lg font-bold text-indigo-600">
                                    {{ new Intl.NumberFormat('ru-RU').format(similarProperty.price) }} ₽
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Стили для анимации перехода */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>