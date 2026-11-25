<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    tone: {
        type: String,
        default: 'on-brand',
    },
});

const isDark = ref(false);

const toneClass = computed(() => (
    props.tone === 'default'
        ? 'border-brand-line text-brand-ink hover:bg-brand-sand'
        : 'border-on-brand/25 text-on-brand hover:bg-white/10'
));

const syncThemeColor = () => {
    const color = getComputedStyle(document.documentElement)
        .getPropertyValue('--theme-color')
        .trim();

    if (color) {
        document.querySelector('meta[name="theme-color"]')
            ?.setAttribute('content', `rgb(${color})`);
    }
};

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
    syncThemeColor();
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    document.documentElement.style.colorScheme = isDark.value ? 'dark' : 'light';

    try {
        localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
    } catch (error) {
        document.documentElement.dataset.themePersistent = 'false';
    }

    syncThemeColor();
};
</script>

<template>
    <button
        type="button"
        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border transition"
        :class="toneClass"
        :aria-label="isDark ? 'Включить светлую тему' : 'Включить тёмную тему'"
        :aria-pressed="isDark"
        @click="toggleTheme"
    >
        <svg
            v-if="isDark"
            class="h-5 w-5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
        >
            <circle cx="12" cy="12" r="4" />
            <path
                stroke-linecap="round"
                d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.65 17.65l1.42 1.42M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.65 6.35l1.42-1.42"
            />
        </svg>
        <svg
            v-else
            class="h-5 w-5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"
            />
        </svg>
    </button>
</template>
