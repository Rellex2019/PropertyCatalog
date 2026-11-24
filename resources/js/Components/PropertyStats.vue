<script setup>
defineProps({
    statistics: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="site-card p-5">
            <div class="flex items-center gap-4">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-sand text-brand">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm text-brand-muted">Всего объектов</p>
                    <p class="mt-1 text-2xl font-semibold text-brand">{{ statistics.total }}</p>
                </div>
            </div>
        </div>

        <div class="site-card p-5">
            <div class="flex items-center gap-4">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-sand text-brand-teal">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm text-brand-muted">Активных</p>
                    <p class="mt-1 text-2xl font-semibold text-brand">{{ statistics.total - (statistics.sold || 0) }}</p>
                </div>
            </div>
        </div>

        <div class="site-card p-5">
            <div class="flex items-center gap-4">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-sand text-brand-gold-dark">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm text-brand-muted">Избранных</p>
                    <p class="mt-1 text-2xl font-semibold text-brand">{{ statistics.featured }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
