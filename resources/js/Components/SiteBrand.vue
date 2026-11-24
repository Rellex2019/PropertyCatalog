<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    variant: {
        type: String,
        default: 'light',
    },
    size: {
        type: String,
        default: 'default',
    },
    subtitle: {
        type: String,
        default: '',
    },
    href: {
        type: String,
        default: null,
    },
});

const page = usePage();
const siteName = computed(() => page.props.seo?.siteName || 'ДомГид');
const isOldName = computed(() => siteName.value.startsWith('Дом'));
const nameStart = computed(() => (isOldName.value ? 'Дом' : siteName.value));
const nameEnd = computed(() => (isOldName.value ? siteName.value.slice(3) : ''));
const destination = computed(() => props.href || route('home'));

const tileClass = computed(() => [
    props.variant === 'dark' ? 'bg-brand text-brand-gold-light' : 'bg-brand-gold text-brand',
    props.size === 'small' ? 'h-9 w-9' : 'h-10 w-10',
]);

const textClass = computed(() => (props.variant === 'dark' ? 'text-brand' : 'text-white'));
const accentClass = computed(() => (props.variant === 'dark' ? 'text-brand-gold-dark' : 'text-brand-gold-light'));
const subtitleClass = computed(() => (props.variant === 'dark' ? 'text-brand-soft' : 'text-white/60'));
</script>

<template>
    <Link :href="destination" class="inline-flex items-center gap-3">
        <span class="grid shrink-0 place-items-center rounded-xl" :class="tileClass">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5M5.5 9.5V20h13V9.5M10 20v-6h4v6" />
            </svg>
        </span>
        <span class="flex flex-col">
            <span
                class="font-semibold tracking-wide"
                :class="[textClass, size === 'small' ? 'text-base' : 'text-lg']"
            >
                {{ nameStart }}<span :class="accentClass">{{ nameEnd }}</span>
            </span>
            <span
                v-if="subtitle"
                class="text-[10px] font-semibold uppercase tracking-[0.2em]"
                :class="subtitleClass"
            >
                {{ subtitle }}
            </span>
        </span>
    </Link>
</template>
