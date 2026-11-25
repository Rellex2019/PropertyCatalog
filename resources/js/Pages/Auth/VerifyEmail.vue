<script setup>
import { computed } from 'vue';
import AuthNotice from '@/Components/AuthNotice.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3';
import AuthInputLayout from '@/Layouts/AuthInputLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <AuthInputLayout>
        <SeoHead
            title="Подтверждение email"
            description="Подтвердите адрес электронной почты, чтобы завершить регистрацию в ДомГид."
            :noindex="true"
        />

        <h1 class="mb-4 text-2xl font-semibold text-brand">Подтверждение email</h1>

        <p class="mb-4 text-sm text-brand-muted">
            Подтвердите email по ссылке из письма. Если письмо не пришло — отправим новую ссылку.
        </p>

        <AuthNotice
            v-if="verificationLinkSent"
            message="Новая ссылка отправлена на ваш email."
            tone="success"
            class="mb-4"
        />

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Отправить ссылку ещё раз
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-brand-muted underline hover:text-brand focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2"
                    >Выйти</Link
                >
            </div>
        </form>
    </AuthInputLayout>
</template>
