<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    image: {
        type: String,
        default: null,
    },
    canonical: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'website',
    },
    noindex: {
        type: Boolean,
        default: false,
    },
    schema: {
        type: [Object, Array],
        default: null,
    },
});

const page = usePage();

const siteName = computed(() => page.props.seo?.siteName || 'ДомГид');
const baseUrl = computed(() => String(page.props.seo?.siteUrl || '').replace(/\/$/, ''));
const fullTitle = computed(() => `${props.title} — ${siteName.value}`);

const absoluteUrl = (url) => {
    if (!url) {
        return null;
    }

    if (/^https?:\/\//.test(url)) {
        return url;
    }

    return `${baseUrl.value}${url.startsWith('/') ? '' : '/'}${url}`;
};

const canonicalUrl = computed(() => {
    const path = props.canonical || page.url?.split('#')[0] || '/';

    return absoluteUrl(path) || path;
});

const imageUrl = computed(() => absoluteUrl(props.image));
const robots = computed(() =>
    props.noindex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large',
);

const schemaJson = computed(() => {
    if (!props.schema) {
        return null;
    }

    return JSON.stringify(props.schema).replace(/</g, '\\u003c');
});
</script>

<template>
    <Head>
        <title>{{ fullTitle }}</title>
        <meta name="description" :content="description" />
        <meta name="robots" :content="robots" />
        <link rel="canonical" :href="canonicalUrl" />

        <meta property="og:type" :content="type" />
        <meta property="og:locale" content="ru_RU" />
        <meta property="og:site_name" :content="siteName" />
        <meta property="og:title" :content="fullTitle" />
        <meta property="og:description" :content="description" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta v-if="imageUrl" property="og:image" :content="imageUrl" />

        <meta name="twitter:card" :content="imageUrl ? 'summary_large_image' : 'summary'" />
        <meta name="twitter:title" :content="fullTitle" />
        <meta name="twitter:description" :content="description" />
        <meta v-if="imageUrl" name="twitter:image" :content="imageUrl" />

        <component is="script" v-if="schemaJson" type="application/ld+json" v-html="schemaJson"></component>
    </Head>
</template>
