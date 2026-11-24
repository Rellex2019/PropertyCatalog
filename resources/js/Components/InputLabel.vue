<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="site-label text-sm font-medium normal-case tracking-normal">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
