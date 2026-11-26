<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SiteBrand from '@/Components/SiteBrand.vue';

const page = usePage();
const currentYear = new Date().getFullYear();
const isHome = computed(() => page.url.split('?')[0].split('#')[0] === '/');
const homeHref = (hash) => (isHome.value ? hash : `${route('home')}${hash}`);
</script>

<template>
    <footer class="site-footer border-t border-brand-line bg-brand-sand">
        <div class="site-container py-12">
            <div class="flex flex-col justify-between gap-10 md:flex-row">
                <div class="max-w-sm">
                    <SiteBrand variant="dark" />
                    <p class="mt-4 text-sm leading-7 text-brand-muted">
                        Помогаем найти жильё, которое подходит по цене, месту и образу жизни.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-12 sm:grid-cols-3">
                    <div>
                        <p class="text-sm font-semibold text-brand">Навигация</p>
                        <div class="mt-4 space-y-3 text-sm text-brand-muted">
                            <component :is="isHome ? 'a' : Link" :href="isHome ? '#top' : route('home')" class="block transition hover:text-brand">Главная</component>
                            <Link :href="route('catalog.index')" class="block transition hover:text-brand">Каталог</Link>
                            <component :is="isHome ? 'a' : Link" :href="homeHref('#how')" class="block transition hover:text-brand">Как это работает</component>
                            <component :is="isHome ? 'a' : Link" :href="homeHref('#cities')" class="block transition hover:text-brand">Города</component>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-brand">Объекты</p>
                        <div class="mt-4 space-y-3 text-sm text-brand-muted">
                            <Link :href="`${route('catalog.index')}?type=Квартира`" class="block transition hover:text-brand">Квартиры</Link>
                            <Link :href="`${route('catalog.index')}?type=Дом`" class="block transition hover:text-brand">Дома</Link>
                            <Link :href="`${route('catalog.index')}?status=Сдается`" class="block transition hover:text-brand">Аренда</Link>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-brand">Аккаунт</p>
                        <div class="mt-4 space-y-3 text-sm text-brand-muted">
                            <Link v-if="$page.props.auth?.user" :href="route('dashboard')" class="block transition hover:text-brand">Кабинет</Link>
                            <Link v-else :href="route('login')" class="block transition hover:text-brand">Войти</Link>
                            <Link v-if="!$page.props.auth?.user" :href="route('register')" class="block transition hover:text-brand">Регистрация</Link>
                        </div>
                    </div>
                </div>
            </div>
            <p class="mt-10 border-t border-brand-line pt-6 text-sm text-brand-soft">
                © {{ currentYear }} {{ $page.props.seo?.siteName || 'ДомГид' }}. Все права защищены.
            </p>
        </div>
    </footer>
</template>
