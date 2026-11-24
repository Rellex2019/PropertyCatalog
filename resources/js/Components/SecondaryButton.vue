<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="site-btn-outline disabled:opacity-50"
    >
        <slot />
    </button>
</template>
