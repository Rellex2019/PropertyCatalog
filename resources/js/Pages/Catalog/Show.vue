<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link } from '@inertiajs/vue3';
import DynamicAuthLayout from '@/Layouts/DynamicAuthLayout.vue';

defineOptions({
    layout: DynamicAuthLayout
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

// ✅ Состояние для карусели
const currentSlideIndex = ref(0);
const isTransitioning = ref(false);

// ✅ Собираем все изображения в массив
const allImages = computed(() => {
    const images = [];
    
    // Добавляем главное изображение
    if (props.property.main_image || props.property.image) {
        images.push(props.property.main_image || props.property.image);
    }
    
    // Добавляем дополнительные изображения
    if (props.property.images && Array.isArray(props.property.images)) {
        images.push(...props.property.images);
    }
    
    // Если изображений нет, добавляем заглушку
    if (images.length === 0) {
        images.push('https://via.placeholder.com/800x600/4F46E5/FFFFFF?text=No+Image');
    }
    
    return images;
});

// Текущее изображение
const currentImage = computed(() => {
    return allImages.value[currentSlideIndex.value] || allImages.value[0];
});

// Общее количество изображений
const totalImages = computed(() => allImages.value.length);

// Показывать ли навигацию
const showNavigation = computed(() => totalImages.value > 1);

// Показывать ли индикаторы
const showIndicators = computed(() => totalImages.value > 1);

// ✅ Переключение на следующий слайд
const nextSlide = () => {
    if (isTransitioning.value || totalImages.value <= 1) return;
    
    isTransitioning.value = true;
    currentSlideIndex.value = (currentSlideIndex.value + 1) % totalImages.value;
    
    setTimeout(() => {
        isTransitioning.value = false;
    }, 300);
};

// ✅ Переключение на предыдущий слайд
const prevSlide = () => {
    if (isTransitioning.value || totalImages.value <= 1) return;
    
    isTransitioning.value = true;
    currentSlideIndex.value = (currentSlideIndex.value - 1 + totalImages.value) % totalImages.value;
    
    setTimeout(() => {
        isTransitioning.value = false;
    }, 300);
};

// ✅ Переход к конкретному слайду
const goToSlide = (index) => {
    if (isTransitioning.value || index === currentSlideIndex.value) return;
    
    isTransitioning.value = true;
    currentSlideIndex.value = index;
    
    setTimeout(() => {
        isTransitioning.value = false;
    }, 300);
};

// ✅ Автопрокрутка
let autoplayInterval = null;

const startAutoplay = () => {
    if (autoplayInterval) {
        clearInterval(autoplayInterval);
    }
    if (totalImages.value > 1) {
        autoplayInterval = setInterval(() => {
            nextSlide();
        }, 5000);
    }
};

const stopAutoplay = () => {
    if (autoplayInterval) {
        clearInterval(autoplayInterval);
        autoplayInterval = null;
    }
};

// ✅ Запускаем и останавливаем автопрокрутку
onMounted(() => {
    startAutoplay();
});

onBeforeUnmount(() => {
    stopAutoplay();
});

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

// Форматирование цены
const formattedPrice = computed(() => {
    return new Intl.NumberFormat('ru-RU').format(props.property.price);
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
                <!-- Галерея изображений с каруселью -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                    <!-- Карусель -->
                    <div class="relative">
                        <!-- Контейнер с изображением -->
                        <div class="relative overflow-hidden rounded-lg bg-gray-100" style="height: 400px;">
                            <img 
                                :src="currentImage" 
                                :alt="property.title"
                                class="w-full h-full object-cover transition-opacity duration-300"
                                :class="{ 'opacity-75': isTransitioning }"
                            />
                            
                            <!-- Количество фото -->
                            <div v-if="showNavigation" class="absolute bottom-4 right-4 bg-black/60 text-white text-xs px-3 py-1 rounded-full">
                                {{ currentSlideIndex + 1 }} / {{ totalImages }}
                            </div>
                            
                            <!-- Бейджи поверх карусели -->
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

                        <!-- Кнопки навигации -->
                        <button
                            v-if="showNavigation"
                            @click="prevSlide"
                            @mouseenter="stopAutoplay"
                            @mouseleave="startAutoplay"
                            class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow-lg transition-all hover:scale-110 focus:outline-none"
                            aria-label="Предыдущее фото"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button
                            v-if="showNavigation"
                            @click="nextSlide"
                            @mouseenter="stopAutoplay"
                            @mouseleave="startAutoplay"
                            class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow-lg transition-all hover:scale-110 focus:outline-none"
                            aria-label="Следующее фото"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Индикаторы -->
                        <div v-if="showIndicators" class="flex justify-center gap-2 mt-4">
                            <button
                                v-for="(image, index) in allImages"
                                :key="index"
                                @click="goToSlide(index)"
                                @mouseenter="stopAutoplay"
                                @mouseleave="startAutoplay"
                                class="h-2 rounded-full transition-all duration-300 focus:outline-none"
                                :class="[
                                    index === currentSlideIndex 
                                        ? 'w-8 bg-indigo-600' 
                                        : 'w-2 bg-gray-300 hover:bg-gray-400'
                                ]"
                                :aria-label="`Перейти к фото ${index + 1}`"
                            />
                        </div>

                        <!-- Миниатюры -->
                        <div v-if="showNavigation" class="flex gap-2 mt-3 overflow-x-auto pb-2">
                            <button
                                v-for="(image, index) in allImages"
                                :key="index"
                                @click="goToSlide(index)"
                                @mouseenter="stopAutoplay"
                                @mouseleave="startAutoplay"
                                class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all duration-200 focus:outline-none"
                                :class="[
                                    index === currentSlideIndex 
                                        ? 'border-indigo-600 ring-2 ring-indigo-200' 
                                        : 'border-transparent hover:border-gray-300'
                                ]"
                            >
                                <img 
                                    :src="image" 
                                    :alt="`Фото ${index + 1}`"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                />
                            </button>
                        </div>
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
/* Плавный переход для изображений */
.transition-opacity {
    transition: opacity 0.3s ease;
}

/* Стили для скролла миниатюр */
.overflow-x-auto::-webkit-scrollbar {
    height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>