<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

// Определяем пропсы для настройки
const props = defineProps({
    // URL для возврата (по умолчанию - предыдущая страница)
    backUrl: {
        type: String,
        default: null
    },
    // Текст кнопки назад
    backText: {
        type: String,
        default: 'Назад'
    },
});

// Функция для возврата на предыдущую страницу
const goBack = () => {
    if (props.backUrl) {
        window.location.href = props.backUrl;
    } else {
        window.history.back();
    }
};
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div class="relative w-full max-w-md">
            <!-- Кнопка назад -->
            <button
                @click="goBack"
                class="absolute left-0 top-0 flex items-center gap-2 text-gray-600 transition-colors hover:text-gray-900"
            >
                <!-- Стрелочка назад -->
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    class="h-5 w-5" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" 
                    />
                </svg>
                <span>{{ backText }}</span>
            </button>

            <!-- Логотип по центру -->
            <div class="flex justify-center">
                <Link href="/">
                    <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
                </Link>
            </div>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>