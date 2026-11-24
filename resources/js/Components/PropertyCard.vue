<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    property: {
        type: Object,
        required: true,
    },
});

const formatPrice = (price) => new Intl.NumberFormat('ru-RU').format(price);
</script>

<template>
    <div class="site-card site-card-hover overflow-hidden">
        <Link :href="route('catalog.show', property.id)" class="block">
            <div class="relative h-60 overflow-hidden">
                <img
                    :src="property.image"
                    :alt="`${property.title} — ${property.type} в городе ${property.city || 'не указан'}`"
                    class="h-full w-full object-cover transition duration-500 hover:scale-105"
                    loading="lazy"
                    decoding="async"
                />
                <span class="site-badge absolute left-4 top-4 bg-brand text-white">
                    {{ property.type }}
                </span>
                <span class="site-badge absolute right-4 top-4 bg-white/95 text-brand">
                    {{ property.status }}
                </span>
                <span
                    v-if="property.is_featured"
                    class="site-badge absolute bottom-4 left-4 bg-brand-gold text-brand"
                >
                    ★ Избранное
                </span>
            </div>

            <div class="p-6">
                <h3 class="text-lg font-semibold leading-snug text-brand transition hover:text-brand-gold-dark">
                    {{ property.title }}
                </h3>
                <p class="mt-2 flex items-start gap-1 text-sm text-brand-soft">
                    <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ property.city }}, {{ property.address }}
                </p>

                <div class="mt-5 grid grid-cols-3 gap-2 border-y border-brand-line py-4 text-center">
                    <div>
                        <span class="block text-sm font-semibold text-brand-ink">{{ property.area }}</span>
                        <span class="text-xs text-brand-soft">м²</span>
                    </div>
                    <div>
                        <span class="block text-sm font-semibold text-brand-ink">{{ property.rooms }}</span>
                        <span class="text-xs text-brand-soft">комнат</span>
                    </div>
                    <div>
                        <span class="block text-sm font-semibold text-brand-ink">{{ property.floor }}</span>
                        <span class="text-xs text-brand-soft">этаж</span>
                    </div>
                </div>

                <p class="mt-4 text-xl font-semibold text-brand">
                    {{ formatPrice(property.price) }} {{ property.currency || '₽' }}
                </p>
            </div>
        </Link>
    </div>
</template>
