<script setup>
import SiteHeader from '@/Components/SiteHeader.vue';
import SiteFooter from '@/Components/SiteFooter.vue';
</script>

<template>
    <div class="site-shell">
        <SiteHeader />

        <header v-if="$slots.header" class="border-b border-brand-line bg-white">
            <div class="site-container py-6">
                <slot name="header" />
            </div>
        </header>

        <main class="site-container py-10 sm:py-14">
            <slot />
        </main>

        <SiteFooter />
    </div>
</template>
