<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const sidebarOpen = ref(false);

const navigation = [
    {name: "Каталог", href: route('main'), icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'},
{ name: 'Главная', href: route('admin.dashboard'), icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Недвижимость', href: route('admin.properties.index'), icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
];

const isActive = (href) => {
    return page.url.startsWith(href);
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Мобильное меню -->
        <div class="lg:hidden">
            <div class="fixed inset-0 z-40 flex">
                <div 
                    v-show="sidebarOpen"
                    class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity"
                    @click="sidebarOpen = false"
                ></div>

                <div 
                    class="relative flex-1 flex flex-col max-w-xs w-full bg-white"
                    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                    style="transition: transform 0.3s ease-in-out"
                >
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button 
                            @click="sidebarOpen = false"
                            class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        >
                            <span class="text-white text-2xl">✕</span>
                        </button>
                    </div>
                    
                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div class="flex-shrink-0 flex items-center px-4">
                            <span class="text-2xl font-bold text-indigo-600">Админка</span>
                        </div>
                        <nav class="mt-5 px-2 space-y-1">
                            <Link 
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                class="group flex items-center px-2 py-2 text-base font-medium rounded-md"
                                :class="[
                                    isActive(item.href) 
                                        ? 'bg-gray-100 text-gray-900' 
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                ]"
                            >
                                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                </svg>
                                {{ item.name }}
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Десктопная навигация -->
        <div class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0">
            <div class="flex-1 flex flex-col min-h-0 bg-white border-r border-gray-200">
                <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <span class="text-2xl font-bold text-indigo-600">Админка</span>
                    </div>
                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        <Link 
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
                            :class="[
                                isActive(item.href) 
                                    ? 'bg-gray-100 text-gray-900' 
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                            ]"
                        >
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            {{ item.name }}
                        </Link>
                    </nav>
                </div>
                <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">{{ page.props.auth.user.name }}</p>
                            <Link :href="route('logout')" method="post" as="button" class="text-xs text-gray-500 hover:text-gray-700">
                                Выйти
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="lg:pl-64 flex flex-col flex-1">
            <header class="bg-white shadow-sm lg:border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <button 
                            @click="sidebarOpen = true"
                            class="lg:hidden -ml-2 p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100"
                        >
                            <span class="text-2xl">☰</span>
                        </button>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            <slot name="header">Админ панель</slot>
                        </h1>
                    </div>
                </div>
            </header>

            <main class="flex-1">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Flash сообщения -->
                        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <span class="text-green-400 text-lg">✓</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ $page.props.flash.success }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <span class="text-red-400 text-lg">✕</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ $page.props.flash.error }}</p>
                                </div>
                            </div>
                        </div>

                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>