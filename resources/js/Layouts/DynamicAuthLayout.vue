<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from './AuthenticatedLayout.vue';
import GuestLayout from './GuestLayout.vue';

const page = usePage();


const isAuthenticated = computed(() => {
    return !!page.props.auth?.user;
});

const LayoutComponent = computed(() => {
    return isAuthenticated.value ? AuthenticatedLayout : GuestLayout;
});
</script>

<template>
    <component :is="LayoutComponent">
        <slot />
    </component>
</template>