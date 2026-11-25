<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SiteBrand from '@/Components/SiteBrand.vue';

const page = usePage();
const sidebarOpen = ref(false);

const isMainAdmin = computed(() => (
    page.props.auth?.is_main_admin || page.props.auth?.user?.role === 'main_admin'
));

const navigation = computed(() => [
    { name: 'Главная', href: route('admin.dashboard'), icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Недвижимость', href: route('admin.properties.index'), icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    ...(isMainAdmin.value
        ? [{ name: 'Пользователи', href: route('admin.users.index'), icon: 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m14-10a4 4 0 11-8 0 4 4 0 018 0zm10 10v-2a4 4 0 00-3-3.87M22 21v-2a4 4 0 00-3-3.87' }]
        : []),
    { name: 'Каталог', href: route('catalog.index'), icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
]);

const isActive = (href) => page.url.startsWith(href);

const closeSidebar = () => {
    sidebarOpen.value = false;
};

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
</script>

<template>
    <div class="site-shell">
        <div class="lg:hidden">
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-40 bg-brand/60 backdrop-blur-sm transition-opacity"
                @click="closeSidebar"
            ></div>

            <div
                class="fixed inset-y-0 left-0 z-50 flex"
                :class="sidebarOpen ? 'visible' : 'invisible'"
            >
                <div
                    class="relative flex h-full w-full max-w-xs flex-col bg-brand shadow-2xl transform transition-transform duration-300 ease-in-out"
                    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                >
                    <div class="absolute right-0 top-0 -mr-12 pt-2">
                        <button
                            v-if="sidebarOpen"
                            type="button"
                            aria-label="Закрыть меню"
                            class="ml-1 flex h-10 w-10 items-center justify-center rounded-full text-white"
                            @click="closeSidebar"
                        >
                            <span class="text-2xl">✕</span>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-4 pb-4 pt-6">
                        <SiteBrand subtitle="Панель управления" />
                        <nav class="mt-8 space-y-1">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                class="flex items-center rounded-xl px-3 py-3 text-base font-medium transition"
                                :class="isActive(item.href)
                                    ? 'bg-white/10 text-brand-gold-light'
                                    : 'text-white/70 hover:bg-white/5 hover:text-white'"
                                @click="closeSidebar"
                            >
                                <svg class="mr-4 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                </svg>
                                {{ item.name }}
                            </Link>
                        </nav>
                    </div>

                    <div class="border-t border-white/10 p-4">
                        <p class="text-sm font-medium text-white">{{ page.props.auth?.user?.name || 'Admin' }}</p>
                        <Link :href="route('logout')" method="post" as="button" class="mt-1 text-xs text-white/60 hover:text-white">
                            Выйти
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
            <div class="flex min-h-0 flex-1 flex-col border-r border-white/10 bg-brand">
                <div class="flex-1 overflow-y-auto px-4 pb-4 pt-6">
                    <SiteBrand subtitle="Панель управления" />
                    <nav class="mt-8 space-y-1">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="flex items-center rounded-xl px-3 py-2.5 text-sm font-medium transition"
                            :class="isActive(item.href)
                                ? 'bg-white/10 text-brand-gold-light'
                                : 'text-white/70 hover:bg-white/5 hover:text-white'"
                        >
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            {{ item.name }}
                        </Link>
                    </nav>
                </div>
                <div class="border-t border-white/10 p-4">
                    <p class="text-sm font-medium text-white">{{ page.props.auth?.user?.name || 'Admin' }}</p>
                    <Link :href="route('logout')" method="post" as="button" class="mt-1 text-xs text-white/60 hover:text-white">
                        Выйти
                    </Link>
                </div>
            </div>
        </div>

        <div class="flex flex-1 flex-col lg:pl-64">
            <header class="border-b border-brand-line bg-white">
                <div class="site-container flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            aria-label="Открыть меню"
                            class="-ml-2 rounded-xl p-2 text-brand transition hover:bg-brand-sand lg:hidden"
                            @click="toggleSidebar"
                        >
                            <span class="text-2xl">☰</span>
                        </button>
                        <SiteBrand variant="dark" size="small" class="lg:hidden" />
                        <h1 class="text-xl font-semibold text-brand sm:text-2xl">
                            <slot name="header">Админ панель</slot>
                        </h1>
                    </div>
                    <Link :href="route('home')" class="site-link hidden sm:inline-flex">На сайт</Link>
                </div>
            </header>

            <main class="flex-1">
                <div class="site-container py-8 sm:py-10">
                    <div v-if="$page.props.flash?.success" class="mb-4 rounded-2xl border-l-4 border-brand-teal bg-white p-4 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span class="text-lg text-brand-teal">✓</span>
                            <p class="text-sm text-brand-muted">{{ $page.props.flash.success }}</p>
                        </div>
                    </div>

                    <div v-if="$page.props.flash?.error" class="mb-4 rounded-2xl border-l-4 border-red-500 bg-white p-4 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span class="text-lg text-red-500">✕</span>
                            <p class="text-sm text-brand-muted">{{ $page.props.flash.error }}</p>
                        </div>
                    </div>

                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
