<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link, router } from '@inertiajs/vue3';
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
const isDownloading = ref(false);

// Состояние для карусели
const currentSlideIndex = ref(0);
const isTransitioning = ref(false);
const showCatalogButton = ref(false);

// Все изображения
const allImages = computed(() => {
    const images = [];
    
    if (props.property.main_image || props.property.image) {
        images.push(props.property.main_image || props.property.image);
    }
    
    if (props.property.images && Array.isArray(props.property.images)) {
        images.push(...props.property.images);
    }
    
    if (images.length === 0) {
        images.push('https://via.placeholder.com/800x600/4F46E5/FFFFFF?text=No+Image');
    }
    
    return images;
});

const currentImage = computed(() => {
    return allImages.value[currentSlideIndex.value] || allImages.value[0];
});

const totalImages = computed(() => allImages.value.length);
const showNavigation = computed(() => totalImages.value > 1);
const showIndicators = computed(() => totalImages.value > 1);

// Навигация по слайдам
const nextSlide = () => {
    if (isTransitioning.value || totalImages.value <= 1) return;
    
    isTransitioning.value = true;
    currentSlideIndex.value = (currentSlideIndex.value + 1) % totalImages.value;
    
    setTimeout(() => {
        isTransitioning.value = false;
    }, 300);
};

const prevSlide = () => {
    if (isTransitioning.value || totalImages.value <= 1) return;
    
    isTransitioning.value = true;
    currentSlideIndex.value = (currentSlideIndex.value - 1 + totalImages.value) % totalImages.value;
    
    setTimeout(() => {
        isTransitioning.value = false;
    }, 300);
};

const goToSlide = (index) => {
    if (isTransitioning.value || index === currentSlideIndex.value) return;
    
    isTransitioning.value = true;
    currentSlideIndex.value = index;
    
    setTimeout(() => {
        isTransitioning.value = false;
    }, 300);
};

// Автопрокрутка
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

// Обработка скролла для плавающей кнопки
const handleScroll = () => {
    const scrollY = window.scrollY;
    showCatalogButton.value = scrollY > 200;
};

// Жизненный цикл
onMounted(() => {
    startAutoplay();
    window.addEventListener('scroll', handleScroll);
    handleScroll();
});

onBeforeUnmount(() => {
    stopAutoplay();
    window.removeEventListener('scroll', handleScroll);
});

// Генерация QR-кода
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
            body: JSON.stringify({ 
                data: url,
                size: 300
            })
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

// Скачивание QR-кода
const downloadQR = () => {
    if (!qrCode.value) {
        alert('Сначала сгенерируйте QR-код');
        return;
    }

    isDownloading.value = true;

    // Создаем ссылку для скачивания
    const link = document.createElement('a');
    link.href = qrCode.value;
    link.download = `qrcode_${props.property.id}_${Date.now()}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    isDownloading.value = false;
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
    <div class="py-6 px-4 sm:py-8 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Хлебные крошки -->
            <nav class="flex flex-wrap items-center mb-4 text-xs sm:text-sm text-gray-500 gap-1">
                <Link href="/" class="hover:text-gray-700">Главная</Link>
                <span class="mx-1">/</span>
                <Link href="/catalog" class="hover:text-gray-700">Каталог</Link>
                <span class="mx-1">/</span>
                <span class="text-gray-900 truncate">{{ property.title }}</span>
            </nav>

            <!-- Плавающая кнопка "Каталог" -->
            <div 
                v-if="showCatalogButton"
                class="fixed top-4 left-4 z-50 transition-all duration-300 transform"
                :class="showCatalogButton ? 'translate-y-0 opacity-100' : '-translate-y-full opacity-0'"
            >
                <Link 
                    href="/catalog"
                    class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-lg hover:bg-indigo-700 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-sm font-medium">Каталог</span>
                </Link>
            </div>

            <!-- Основная карточка -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Галерея и информация -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 p-4 sm:p-6">
                    <!-- Левая колонка - Галерея -->
                    <div class="relative">
                        <div class="relative overflow-hidden rounded-lg bg-gray-100" style="height: 400px;">
                            <img 
                                :src="currentImage" 
                                :alt="property.title"
                                class="w-full h-full object-cover transition-opacity duration-300"
                                :class="{ 'opacity-75': isTransitioning }"
                            />
                            
                            <div v-if="showNavigation" class="absolute bottom-3 sm:bottom-4 right-3 sm:right-4 bg-black/60 text-white text-[10px] sm:text-xs px-2 sm:px-3 py-0.5 sm:py-1 rounded-full">
                                {{ currentSlideIndex + 1 }} / {{ totalImages }}
                            </div>
                            
                            <div class="absolute top-3 sm:top-4 left-3 sm:left-4 flex flex-col gap-1 sm:gap-2">
                                <span class="px-2 sm:px-3 py-0.5 sm:py-1 bg-indigo-600 text-white text-[10px] sm:text-xs font-semibold rounded-full">
                                    {{ property.type }}
                                </span>
                                <span 
                                    v-if="property.is_featured"
                                    class="px-2 sm:px-3 py-0.5 sm:py-1 bg-yellow-500 text-white text-[10px] sm:text-xs font-semibold rounded-full"
                                >
                                    ★ Избранное
                                </span>
                            </div>
                            
                            <span 
                                :class="['absolute top-3 sm:top-4 right-3 sm:right-4 px-2 sm:px-3 py-0.5 sm:py-1 text-white text-[10px] sm:text-xs font-semibold rounded-full', statusClass]"
                            >
                                {{ property.status }}
                            </span>
                        </div>

                        <!-- Кнопки навигации карусели -->
                        <button
                            v-if="showNavigation"
                            @click="prevSlide"
                            @mouseenter="stopAutoplay"
                            @mouseleave="startAutoplay"
                            class="absolute left-1 sm:left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-1.5 sm:p-2 rounded-full shadow-lg transition-all hover:scale-110 focus:outline-none"
                            aria-label="Предыдущее фото"
                        >
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button
                            v-if="showNavigation"
                            @click="nextSlide"
                            @mouseenter="stopAutoplay"
                            @mouseleave="startAutoplay"
                            class="absolute right-1 sm:right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-1.5 sm:p-2 rounded-full shadow-lg transition-all hover:scale-110 focus:outline-none"
                            aria-label="Следующее фото"
                        >
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Индикаторы -->
                        <div v-if="showIndicators" class="flex justify-center gap-1.5 sm:gap-2 mt-3 sm:mt-4">
                            <button
                                v-for="(image, index) in allImages"
                                :key="index"
                                @click="goToSlide(index)"
                                @mouseenter="stopAutoplay"
                                @mouseleave="startAutoplay"
                                class="h-1.5 sm:h-2 rounded-full transition-all duration-300 focus:outline-none"
                                :class="[
                                    index === currentSlideIndex 
                                        ? 'w-4 sm:w-8 bg-indigo-600' 
                                        : 'w-1.5 sm:w-2 bg-gray-300 hover:bg-gray-400'
                                ]"
                                :aria-label="`Перейти к фото ${index + 1}`"
                            />
                        </div>

                        <!-- Миниатюры -->
                        <div v-if="showNavigation" class="flex gap-1.5 sm:gap-2 mt-2 sm:mt-3 overflow-x-auto pb-2">
                            <button
                                v-for="(image, index) in allImages"
                                :key="index"
                                @click="goToSlide(index)"
                                @mouseenter="stopAutoplay"
                                @mouseleave="startAutoplay"
                                class="flex-shrink-0 w-10 h-10 sm:w-14 sm:h-14 lg:w-16 lg:h-16 rounded-lg overflow-hidden border-2 transition-all duration-200 focus:outline-none"
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

                    <!-- Правая колонка - Информация -->
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">
                            {{ property.title }}
                        </h1>
                        
                        <p class="text-sm sm:text-base text-gray-600 mb-3 sm:mb-4 flex items-center gap-1 sm:gap-2">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ property.address }}, {{ property.city }}</span>
                        </p>

                        <div class="flex items-center mb-4 sm:mb-6">
                            <span class="text-2xl sm:text-3xl font-bold text-indigo-600">
                                {{ formattedPrice }} ₽
                            </span>
                        </div>

                        <!-- Характеристики -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-4 mb-4 sm:mb-6">
                            <div class="p-2 sm:p-3 bg-gray-50 rounded-lg">
                                <span class="block text-[10px] sm:text-sm text-gray-500">Площадь</span>
                                <span class="text-base sm:text-lg font-semibold">{{ property.area }} м²</span>
                            </div>
                            <div class="p-2 sm:p-3 bg-gray-50 rounded-lg">
                                <span class="block text-[10px] sm:text-sm text-gray-500">Комнат</span>
                                <span class="text-base sm:text-lg font-semibold">{{ property.rooms }}</span>
                            </div>
                            <div class="p-2 sm:p-3 bg-gray-50 rounded-lg">
                                <span class="block text-[10px] sm:text-sm text-gray-500">Этаж</span>
                                <span class="text-base sm:text-lg font-semibold">{{ property.floor }} / {{ property.total_floors }}</span>
                            </div>
                            <div class="p-2 sm:p-3 bg-gray-50 rounded-lg">
                                <span class="block text-[10px] sm:text-sm text-gray-500">Дата</span>
                                <span class="text-base sm:text-lg font-semibold">{{ new Date(property.published_at).toLocaleDateString('ru-RU') }}</span>
                            </div>
                        </div>

                        <!-- Удобства -->
                        <div v-if="amenities.length" class="mb-4 sm:mb-6">
                            <h3 class="text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Удобства:</h3>
                            <div class="flex flex-wrap gap-1 sm:gap-2">
                                <span 
                                    v-for="amenity in amenities" 
                                    :key="amenity"
                                    class="px-2 sm:px-3 py-0.5 sm:py-1 bg-indigo-50 text-indigo-700 text-[10px] sm:text-xs rounded-full"
                                >
                                    {{ amenity }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Описание -->
                <div class="px-4 sm:px-6 py-4 sm:py-6 border-t border-gray-100">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Описание</h2>
                    <p class="text-sm sm:text-base text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ property.description }}
                    </p>
                </div>

                <!-- QR-код -->
                <div class="px-4 sm:px-6 py-4 sm:py-6 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-3 sm:mb-4 gap-2 sm:gap-0">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">QR-код объекта</h2>
                        <button
                            v-if="isQrUnlocked"
                            @click="resetQR"
                            class="text-xs sm:text-sm text-gray-500 hover:text-gray-700"
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
                            <div class="w-36 h-36 sm:w-44 sm:h-44 lg:w-48 lg:h-48 bg-gray-200 rounded-lg flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </div>
                                <div class="absolute inset-0 bg-gray-300/50 backdrop-blur-sm"></div>
                                
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="bg-white/90 p-2 sm:p-3 rounded-full shadow-lg">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                    <span class="text-white font-medium opacity-0 group-hover:opacity-100 transition-opacity text-xs sm:text-sm text-center px-2">
                                        Нажмите, чтобы разблокировать
                                    </span>
                                </div>
                            </div>
                            <p class="mt-1.5 sm:mt-2 text-[10px] sm:text-sm text-gray-500 text-center">
                                Нажмите для генерации QR-кода
                            </p>
                        </div>

                        <!-- Разблокированный QR -->
                        <div v-else class="flex flex-col items-center">
                            <div class="w-36 h-36 sm:w-44 sm:h-44 lg:w-48 lg:h-48 relative">
                                <img 
                                    v-if="qrCode" 
                                    :src="qrCode" 
                                    alt="QR Code"
                                    class="w-full h-full object-contain rounded-lg"
                                    :class="{ 'opacity-50': isDownloading }"
                                />
                                <div v-else-if="isQrLoading" class="w-full h-full flex items-center justify-center bg-gray-100 rounded-lg">
                                    <div class="animate-spin rounded-full h-8 w-8 sm:h-10 sm:w-10 lg:h-12 lg:w-12 border-b-2 border-indigo-600"></div>
                                </div>
                            </div>
                            <p class="mt-1.5 sm:mt-2 text-[10px] sm:text-sm text-gray-500 text-center">
                                QR-код для просмотра объекта
                            </p>
                            
                            <!-- Кнопки управления QR -->
                            <div class="flex flex-wrap items-center justify-center gap-2 mt-2 sm:mt-3">
                                <button 
                                    @click="generateQR"
                                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-indigo-600 hover:text-indigo-800"
                                >
                                    Обновить
                                </button>
                                
                                <!-- Кнопка скачивания -->
                                <button 
                                    @click="downloadQR"
                                    :disabled="isDownloading || !qrCode"
                                    class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-1.5 sm:py-2 bg-green-600 text-white text-xs sm:text-sm rounded-md hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    {{ isDownloading ? 'Скачивание...' : 'Скачать PNG' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Похожие объекты -->
            <div v-if="similarProperties.length" class="mt-8 sm:mt-12">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Похожие объекты</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <div 
                        v-for="similarProperty in similarProperties" 
                        :key="similarProperty.id"
                        class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow"
                    >
                        <Link :href="route('catalog.show', similarProperty.id)">
                            <img 
                                :src="similarProperty.image" 
                                :alt="similarProperty.title"
                                class="w-full h-36 sm:h-48 object-cover"
                            />
                            <div class="p-3 sm:p-4">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-0.5 sm:mb-1">
                                    {{ similarProperty.title }}
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-500 mb-1.5 sm:mb-2 truncate">{{ similarProperty.address }}</p>
                                <p class="text-base sm:text-lg font-bold text-indigo-600">
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
.transition-opacity {
    transition: opacity 0.3s ease;
}

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