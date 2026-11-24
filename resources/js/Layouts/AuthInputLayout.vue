<script setup>
import SiteHeader from '@/Components/SiteHeader.vue';

const props = defineProps({
    backUrl: {
        type: String,
        default: null,
    },
    backText: {
        type: String,
        default: 'Назад',
    },
});

const goBack = () => {
    if (props.backUrl) {
        window.location.href = props.backUrl;
    } else {
        window.history.back();
    }
};
</script>

<template>
    <div class="site-shell">
        <SiteHeader />

        <main class="mx-auto w-full max-w-md px-4 py-10 sm:py-14">
            <button
                type="button"
                class="mb-5 inline-flex items-center gap-2 text-sm text-brand-muted transition hover:text-brand"
                @click="goBack"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ backText }}
            </button>

            <div class="site-card px-6 py-7 sm:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>
