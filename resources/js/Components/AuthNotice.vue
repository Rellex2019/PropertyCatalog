<script setup>
import { computed } from 'vue';

const props = defineProps({
    message: {
        type: String,
        default: '',
    },
    tone: {
        type: String,
        default: 'info',
    },
});

const toneClasses = computed(() => ({
    info: 'border-brand-line bg-brand-sand text-brand-ink',
    success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
    error: 'border-red-200 bg-red-50 text-red-700',
}[props.tone] ?? 'border-brand-line bg-brand-sand text-brand-ink'));

const iconPath = computed(() => ({
    info: 'M12 16v-4m0-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    success: 'm5 13 4 4L19 7',
    error: 'M12 8v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z',
}[props.tone] ?? 'M12 16v-4m0-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'));
</script>

<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="-translate-y-1 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="-translate-y-1 opacity-0"
    >
        <div
            v-if="message"
            class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm"
            :class="toneClasses"
            :role="tone === 'error' ? 'alert' : 'status'"
        >
            <svg
                class="mt-0.5 h-4 w-4 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path stroke-linecap="round" stroke-linejoin="round" :d="iconPath" />
            </svg>
            <span>{{ message }}</span>
        </div>
    </Transition>
</template>
