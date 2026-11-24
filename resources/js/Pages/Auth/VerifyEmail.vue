<script setup>
import { computed } from 'vue';
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

        <div class="mb-4 text-sm text-brand-muted">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </div>

        <div
            class="mb-4 text-sm font-medium text-green-600"
            v-if="verificationLinkSent"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-brand-muted underline hover:text-brand focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2"
                    >Log Out</Link
                >
            </div>
        </form>
    </AuthInputLayout>
</template>
