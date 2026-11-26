<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SiteBrand from '@/Components/SiteBrand.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const page = usePage();
const mobileOpen = ref(false);
const user = computed(() => page.props.auth?.user ?? null);
const canAccessAdmin = computed(() => (
    page.props.auth?.can_access_admin
    || ['manager', 'admin', 'main_admin'].includes(user.value?.role)
));

const isHome = computed(() => page.url.split('?')[0].split('#')[0] === '/');
const homeHref = (hash) => (isHome.value ? hash : `${route('home')}${hash}`);

const navigation = computed(() => [
    { label: 'Главная', href: isHome.value ? '#top' : route('home'), active: isHome.value, local: isHome.value },
    { label: 'Каталог', href: route('catalog.index'), active: page.url.startsWith('/catalog'), local: false },
    { label: 'Как это работает', href: homeHref('#how'), active: false, local: isHome.value },
    { label: 'Города', href: homeHref('#cities'), active: false, local: isHome.value },
    ...(canAccessAdmin.value
        ? [{ label: 'Админка', href: route('admin.dashboard'), active: page.url.startsWith('/admin'), local: false }]
        : []),
]);
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-white/10 bg-brand/95 backdrop-blur">
        <div class="site-container flex h-20 items-center justify-between">
            <SiteBrand />

            <nav class="hidden items-center gap-8 text-sm md:flex">
                <component
                    :is="link.local ? 'a' : Link"
                    v-for="link in navigation"
                    :key="link.label"
                    :href="link.href"
                    class="transition"
                    :class="link.active ? 'text-white' : 'text-white/70 hover:text-white'"
                >
                    {{ link.label }}
                </component>
            </nav>

            <div class="hidden items-center gap-3 md:flex">
                <ThemeToggle />
                <template v-if="user">
                    <Link :href="route('dashboard')" class="site-btn-primary">
                        В кабинет
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="rounded-xl border border-white/25 px-4 py-2.5 text-sm text-white/80 transition hover:bg-white/10 hover:text-white"
                    >
                        Выйти
                    </Link>
                </template>
                <template v-else>
                    <Link :href="route('login')" class="rounded-xl px-4 py-2.5 text-sm text-white/80 transition hover:text-white">
                        Войти
                    </Link>
                    <Link :href="route('register')" class="site-btn-primary">
                        Регистрация
                    </Link>
                </template>
            </div>

            <div class="flex items-center gap-2 md:hidden">
                <ThemeToggle />
                <button
                    type="button"
                    aria-label="Открыть меню"
                    class="rounded-xl border border-white/20 p-2 text-white"
                    @click="mobileOpen = !mobileOpen"
                >
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>
            </div>
        </div>

        <div v-if="mobileOpen" class="border-t border-white/10 bg-brand px-4 pb-5 pt-2 md:hidden">
            <component
                :is="link.local ? 'a' : Link"
                v-for="link in navigation"
                :key="link.label"
                :href="link.href"
                class="block py-3"
                :class="link.active ? 'text-white' : 'text-white/70'"
                @click="mobileOpen = false"
            >
                {{ link.label }}
            </component>
            <div class="mt-3 flex flex-wrap gap-3 border-t border-white/10 pt-4">
                <template v-if="user">
                    <Link :href="route('dashboard')" class="site-btn-primary" @click="mobileOpen = false">
                        В кабинет
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="site-btn-ghost"
                        @click="mobileOpen = false"
                    >
                        Выйти
                    </Link>
                </template>
                <template v-else>
                    <Link :href="route('login')" class="site-btn-ghost" @click="mobileOpen = false">
                        Войти
                    </Link>
                    <Link :href="route('register')" class="site-btn-primary" @click="mobileOpen = false">
                        Регистрация
                    </Link>
                </template>
            </div>
        </div>
    </header>
</template>
