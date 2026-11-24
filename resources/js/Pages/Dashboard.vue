<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
</script>

<template>
    <SeoHead
        title="Личный кабинет"
        description="Личный кабинет пользователя ДомГид."
        :noindex="true"
    />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-brand">
                Личный кабинет
            </h1>
        </template>

        <div class="site-card p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-gold-dark">Аккаунт</p>
            <h2 class="mt-3 text-2xl font-semibold text-brand">Вы вошли в аккаунт</h2>
            <p class="mt-3 max-w-xl text-sm text-brand-muted">
                Здесь будет информация о ваших объявлениях и сохранённых объектах.
            </p>
        </div>
    </AuthenticatedLayout>
</template>
