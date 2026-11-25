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

const navigation = computed(() => [
    { label: 'Главная', href: route('home'), active: page.url === '/' },
    { label: 'Каталог', href: route('catalog.index'), active: page.url.startsWith('/catalog') },
    { label: 'Как это работает', href: `${route('home')}#how`, active: false },
    { label: 'Города', href: `${route('home')}#cities`, active: false },
    ...(canAccessAdmin.value
        ? [{ label: 'Админка', href: route('admin.dashboard'), active: page.url.startsWith('/admin') }]
        : []),
]);
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-white/10 bg-brand/95 backdrop-blur">
        <div class="site-container flex h-20 items-center justify-between">
            <SiteBrand />

            <nav class="hidden items-center gap-8 text-sm md:flex">
                <Link
                    v-for="link in navigation"
                    :key="link.label"
                    :href="link.href"
                    class="transition"
                    :class="link.active ? 'text-white' : 'text-white/70 hover:text-white'"
                >
                    {{ link.label }}
                </Link>
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
            <Link
                v-for="link in navigation"
                :key="link.label"
                :href="link.href"
                class="block py-3"
                :class="link.active ? 'text-white' : 'text-white/70'"
                @click="mobileOpen = false"
            >
                {{ link.label }}
            </Link>
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
