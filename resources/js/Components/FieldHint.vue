<script setup>
defineProps({
    show: {
        type: Boolean,
        default: true,
    },
});
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
        <p
            v-if="show"
            class="mt-2 flex items-start gap-2 text-xs leading-relaxed text-brand-soft"
        >
            <svg
                class="mt-0.5 h-3.5 w-3.5 shrink-0 text-brand-gold"
                fill="none"
                viewBox="0 0 20 20"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-9.2 3.5 4.5-4.5m-4.5 0 2 2m2-2 2.5 2.5"
                />
            </svg>
            <span><slot /></span>
        </p>
    </Transition>
</template>
