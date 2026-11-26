<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import SeoHead from '@/Components/SeoHead.vue';
import SiteHeader from '@/Components/SiteHeader.vue';
import SiteFooter from '@/Components/SiteFooter.vue';

const props = defineProps({
    featuredProperties: {
        type: Array,
        default: () => [],
    },
    statistics: {
        type: Object,
        default: () => ({}),
    },
    popularCities: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const placeholderImage = 'https://via.placeholder.com/1200x800/15594F/F7F4EF?text=DomGid';
const heroImage = ref(placeholderImage);

watch(
    () => props.featuredProperties,
    (items) => {
        heroImage.value = items?.[0]?.image || placeholderImage;
    },
    { immediate: true },
);

const formatNumber = (value) => new Intl.NumberFormat('ru-RU').format(Number(value || 0));

const formatPrice = (property) => {
    return `${formatNumber(property.price)} ${property.currency || '₽'}`;
};

const stats = computed(() => [
    { value: formatNumber(props.statistics?.total), label: 'объектов в каталоге' },
    { value: formatNumber(props.statistics?.for_sale), label: 'вариантов на продажу' },
    { value: formatNumber(props.statistics?.for_rent), label: 'объектов в аренду' },
    { value: formatNumber(props.statistics?.cities), label: 'городов для поиска' },
]);

const dealTypes = [
    { value: 'Продается', label: 'Купить' },
    { value: 'Сдается', label: 'Снять' },
];

const propertyTypes = ['Квартира', 'Дом', 'Таунхаус', 'Коммерческая', 'Земельный участок'];

const searchForm = ref({
    deal: 'Продается',
    type: '',
    city: '',
    rooms: '',
    maxPrice: '',
});

const selectDeal = (value) => {
    searchForm.value.deal = value;
};

const submitSearch = () => {
    const params = {};

    if (searchForm.value.deal) {
        params.status = searchForm.value.deal;
    }
    if (searchForm.value.type) {
        params.type = searchForm.value.type;
    }
    if (searchForm.value.city) {
        params.search = searchForm.value.city;
    }
    if (searchForm.value.rooms) {
        params.rooms = searchForm.value.rooms;
    }
    if (searchForm.value.maxPrice) {
        params.priceMax = searchForm.value.maxPrice;
    }

    router.get(route('catalog.index'), params, { preserveScroll: true });
};

const cityUrl = (name) => {
    return `${route('catalog.index')}?search=${encodeURIComponent(name)}`;
};

const handleImageError = (event) => {
    event.target.src = placeholderImage;
};

const steps = [
    {
        number: '01',
        title: 'Расскажите о планах',
        text: 'Выберите тип недвижимости, город и бюджет — мы сразу сузим поиск до подходящих вариантов.',
        icon: 'M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z',
    },
    {
        number: '02',
        title: 'Изучите подборку',
        text: 'Сравнивайте планировки, площадь и удобства. Все важные характеристики собраны в одной карточке.',
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    },
    {
        number: '03',
        title: 'Свяжитесь напрямую',
        text: 'Откройте объявление, изучите детали и свяжитесь с публикатором удобным для вас способом.',
        icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    },
];

const homeSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    name: page.props.seo?.siteName || 'ДомГид',
    url: route('home'),
    description: 'Каталог квартир, домов и коммерческой недвижимости в России.',
    potentialAction: {
        '@type': 'SearchAction',
        target: `${route('catalog.index')}?search={search_term_string}`,
        'query-input': 'required name=search_term_string',
    },
}));
</script>

<template>
    <SeoHead
        title="Недвижимость: квартиры и дома"
        description="ДомГид — каталог квартир, домов и коммерческой недвижимости. Сравнивайте варианты и находите жильё под свой бюджет."
        :image="heroImage"
        :canonical="route('home')"
        :schema="homeSchema"
    />

    <div class="min-h-screen bg-brand-cream text-brand-ink">
        <SiteHeader />

        <main>
            <section class="relative overflow-hidden bg-brand pb-44 pt-16 text-white sm:pt-20">
                <div class="pointer-events-none absolute -left-40 top-10 h-96 w-96 rounded-full site-glow-gold blur-3xl"></div>
                <div class="pointer-events-none absolute -right-32 bottom-0 h-96 w-96 rounded-full site-glow-teal blur-3xl"></div>

                <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                    <div>
                        <p class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-sm text-brand-gold-light">
                            <span class="h-1.5 w-1.5 rounded-full bg-brand-gold-light"></span>
                            Недвижимость без лишних сложностей
                        </p>
                        <h1 class="mt-6 max-w-2xl text-4xl font-semibold leading-[1.1] sm:text-5xl lg:text-6xl">
                            Найдите дом,
                            <span class="text-brand-gold-light">в котором хочется жить</span>
                        </h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-white/70">
                            Квартиры, дома и коммерческие помещения в проверенных объявлениях.
                            Сравнивайте варианты и выбирайте то, что подходит именно вам.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-4">
                            <Link
                                :href="route('catalog.index')"
                                class="rounded-xl bg-brand-gold px-7 py-3.5 text-sm font-semibold text-brand transition hover:bg-brand-gold-light"
                            >
                                Смотреть каталог
                            </Link>
                            <a
                                href="#how"
                                class="rounded-xl border border-white/25 px-7 py-3.5 text-sm font-semibold text-white transition hover:bg-white/10"
                            >
                                Как это работает
                            </a>
                        </div>
                        <div class="mt-10 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-white/60">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-brand-gold-light" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Актуальные объявления
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-brand-gold-light" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Удобный поиск по параметрам
                            </span>
                        </div>
                    </div>

                    <div class="relative hidden lg:block">
                        <div class="absolute -inset-6 rounded-[2.5rem] site-glow-gold-strong blur-2xl"></div>
                        <img
                            :src="heroImage"
                            alt="Пример объекта недвижимости из каталога ДомГид"
                            class="relative h-[430px] w-full rounded-[2rem] object-cover shadow-2xl"
                            fetchpriority="high"
                            @error="handleImageError"
                        />
                        <div class="absolute -bottom-8 -left-10 rounded-2xl bg-white p-5 text-brand-ink shadow-xl">
                            <p class="text-xs uppercase tracking-widest text-brand-soft">В каталоге</p>
                            <p class="mt-1 text-2xl font-semibold">{{ formatNumber(statistics?.total) }} объектов</p>
                        </div>
                        <div class="absolute -right-6 top-10 rounded-2xl bg-brand-gold px-5 py-4 text-sm font-semibold text-brand shadow-xl">
                            Проверенные объявления
                        </div>
                    </div>
                </div>

                <div class="relative mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="rounded-3xl bg-white p-5 text-brand-ink shadow-2xl sm:p-7">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-semibold sm:text-2xl">Подберите объект мечты</h2>
                                <p class="mt-1 text-sm text-brand-muted">Несколько фильтров — и подборка готова</p>
                            </div>
                            <div class="inline-flex rounded-xl bg-brand-sand p-1">
                                <button
                                    v-for="deal in dealTypes"
                                    :key="deal.value"
                                    type="button"
                                    class="rounded-lg px-5 py-2 text-sm font-semibold transition"
                                    :class="searchForm.deal === deal.value ? 'bg-brand text-white' : 'text-brand-muted hover:text-brand-ink'"
                                    @click="selectDeal(deal.value)"
                                >
                                    {{ deal.label }}
                                </button>
                            </div>
                        </div>

                        <form @submit.prevent="submitSearch" class="mt-6 grid gap-4 md:grid-cols-5">
                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-wider text-brand-soft">Тип</span>
                                <select v-model="searchForm.type" class="w-full rounded-xl border-0 bg-brand-cream px-4 py-3 text-sm focus:ring-2 focus:ring-brand-gold">
                                    <option value="">Любой тип</option>
                                    <option v-for="type in propertyTypes" :key="type" :value="type">{{ type }}</option>
                                </select>
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-wider text-brand-soft">Город</span>
                                <input
                                    v-model="searchForm.city"
                                    type="text"
                                    placeholder="Например, Москва"
                                    class="w-full rounded-xl border-0 bg-brand-cream px-4 py-3 text-sm placeholder:text-brand-soft focus:ring-2 focus:ring-brand-gold"
                                />
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-wider text-brand-soft">Комнаты</span>
                                <select v-model="searchForm.rooms" class="w-full rounded-xl border-0 bg-brand-cream px-4 py-3 text-sm focus:ring-2 focus:ring-brand-gold">
                                    <option value="">Любое количество</option>
                                    <option v-for="room in 5" :key="room" :value="room">{{ room }}</option>
                                </select>
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-wider text-brand-soft">Цена до</span>
                                <select v-model="searchForm.maxPrice" class="w-full rounded-xl border-0 bg-brand-cream px-4 py-3 text-sm focus:ring-2 focus:ring-brand-gold">
                                    <option value="">Любая цена</option>
                                    <option value="10000000">10 млн ₽</option>
                                    <option value="25000000">25 млн ₽</option>
                                    <option value="50000000">50 млн ₽</option>
                                    <option value="100000000">100 млн ₽</option>
                                </select>
                            </label>
                            <button
                                type="submit"
                                class="self-end rounded-xl bg-brand-gold px-6 py-3 text-sm font-semibold text-brand transition hover:bg-brand-gold-light"
                            >
                                Найти объект
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <section class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-6 rounded-3xl bg-white p-7 shadow-lg sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="item in stats" :key="item.label" class="border-brand-line sm:border-r sm:pr-6 lg:last:border-r-0">
                        <p class="text-3xl font-semibold text-brand">{{ item.value }}</p>
                        <p class="mt-1 text-sm text-brand-muted">{{ item.label }}</p>
                    </div>
                </div>
            </section>

            <section id="featured" class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-gold-dark">Рекомендуем</p>
                        <h2 class="mt-3 text-3xl font-semibold text-brand sm:text-4xl">Избранные объекты</h2>
                        <p class="mt-3 max-w-xl text-brand-muted">Отобрали интересные варианты, которые стоит посмотреть в первую очередь.</p>
                    </div>
                    <Link :href="route('catalog.index')" class="inline-flex items-center gap-2 text-sm font-semibold text-brand hover:text-brand-gold-dark">
                        Весь каталог
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                        </svg>
                    </Link>
                </div>

                <div v-if="featuredProperties.length" class="mt-10 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="property in featuredProperties"
                        :key="property.id"
                        :href="route('catalog.show', property.id)"
                        class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5 transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                    >
                        <div class="relative h-60 overflow-hidden">
                            <img
                                :src="property.image || placeholderImage"
                                :alt="`${property.title} — ${property.type} в городе ${property.city || 'не указан'}`"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                loading="lazy"
                                decoding="async"
                                @error="handleImageError"
                            />
                            <span class="site-badge site-badge--neutral absolute left-4 top-4">
                                {{ property.status }}
                            </span>
                            <span class="absolute right-4 top-4 rounded-full bg-brand/90 px-3 py-1 text-xs font-semibold text-white">
                                {{ property.status }}
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="text-lg font-semibold leading-snug text-brand-ink group-hover:text-brand-gold-dark">{{ property.title }}</h3>
                                <span v-if="property.is_featured" class="rounded-full bg-brand-gold-light/20 px-2.5 py-1 text-xs font-semibold text-brand-gold-dark">★</span>
                            </div>
                            <p class="mt-2 text-sm text-brand-soft">{{ property.city }}, {{ property.address }}</p>
                            <div class="mt-5 flex items-center gap-5 border-t border-brand-line pt-4 text-sm text-brand-muted">
                                <span>{{ property.area }} м²</span>
                                <span>{{ property.rooms }} комн.</span>
                                <span>{{ property.floor }}/{{ property.total_floors }} эт.</span>
                            </div>
                            <p class="mt-4 text-xl font-semibold text-brand">{{ formatPrice(property) }}</p>
                        </div>
                    </Link>
                </div>

                <div v-else class="mt-10 rounded-3xl bg-white p-12 text-center shadow-sm">
                    <h3 class="text-xl font-semibold text-brand">Объявления скоро появятся</h3>
                    <p class="mt-2 text-brand-muted">Загляните в каталог — возможно, там уже есть подходящий вариант.</p>
                    <Link :href="route('catalog.index')" class="mt-6 inline-block rounded-xl bg-brand px-6 py-3 text-sm font-semibold text-white">
                        Открыть каталог
                    </Link>
                </div>
            </section>

            <section id="how" class="bg-brand py-24 text-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-gold-light">Пошагово</p>
                        <h2 class="mt-3 text-3xl font-semibold sm:text-4xl">Как найти свой объект</h2>
                        <p class="mt-4 text-white/65">Простой путь от первого поиска до выбранной квартиры или дома.</p>
                    </div>

                    <div class="mt-12 grid gap-6 md:grid-cols-3">
                        <div v-for="step in steps" :key="step.number" class="rounded-3xl border border-white/10 bg-white/5 p-8">
                            <div class="flex items-center justify-between">
                                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-gold text-brand">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="step.icon" />
                                    </svg>
                                </span>
                                <span class="text-4xl font-semibold text-white/10">{{ step.number }}</span>
                            </div>
                            <h3 class="mt-6 text-xl font-semibold">{{ step.title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-white/60">{{ step.text }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="cities" class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-gold-dark">География</p>
                        <h2 class="mt-3 text-3xl font-semibold text-brand sm:text-4xl">Популярные города</h2>
                    </div>
                    <p class="max-w-md text-brand-muted">Выберите город и посмотрите, какие объекты доступны прямо сейчас.</p>
                </div>

                <div v-if="popularCities.length" class="mt-10 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    <Link
                        v-for="city in popularCities"
                        :key="city.name"
                        :href="cityUrl(city.name)"
                        class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 transition hover:-translate-y-1 hover:shadow-lg"
                    >
                        <p class="font-semibold text-brand">{{ city.name }}</p>
                        <p class="mt-1 text-sm text-brand-soft">{{ city.count }} объектов</p>
                    </Link>
                </div>
                <p v-else class="mt-10 rounded-2xl bg-white p-8 text-center text-brand-muted shadow-sm">
                    Города появятся здесь после наполнения каталога.
                </p>
            </section>

            <section class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-r from-brand to-brand-forest px-8 py-14 text-white sm:px-14">
                    <div class="pointer-events-none absolute -right-16 -top-24 h-80 w-80 rounded-full site-glow-gold-strong blur-3xl"></div>
                    <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
                        <div>
                            <h2 class="text-3xl font-semibold sm:text-4xl">Готовы найти свой дом?</h2>
                            <p class="mt-3 max-w-xl text-white/70">Сохраняйте понравившиеся объявления и возвращайтесь к ним в удобное время.</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <template v-if="user">
                                <Link :href="route('dashboard')" class="rounded-xl bg-brand-gold px-7 py-3.5 text-sm font-semibold text-brand transition hover:bg-brand-gold-light">
                                    В личный кабинет
                                </Link>
                            </template>
                            <template v-else>
                                <Link :href="route('register')" class="rounded-xl bg-brand-gold px-7 py-3.5 text-sm font-semibold text-brand transition hover:bg-brand-gold-light">
                                    Создать аккаунт
                                </Link>
                                <Link :href="route('login')" class="rounded-xl border border-white/25 px-7 py-3.5 text-sm font-semibold text-white transition hover:bg-white/10">
                                    Войти
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <SiteFooter />
    </div>
</template>
