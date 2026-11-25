<script setup>
import AuthNotice from '@/Components/AuthNotice.vue';
import Checkbox from '@/Components/Checkbox.vue';
import FieldHint from '@/Components/FieldHint.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthInputLayout from '@/Layouts/AuthInputLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    identifier: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthInputLayout>
        <SeoHead
            title="Вход в аккаунт"
            description="Войдите в ДомГид по email или телефону, чтобы сохранять избранное."
            :noindex="true"
        />

        <h1 class="text-2xl font-semibold text-brand">Вход в аккаунт</h1>
        <p class="mt-3 max-w-xl text-sm text-brand-muted">
            Введите email или телефон, который указали при регистрации.
        </p>

        <AuthNotice v-if="status" :message="status" tone="info" class="mt-4" />

        <form class="mt-6" @submit.prevent="submit">
            <div>
                <InputLabel for="identifier" value="Email или телефон" />
                <TextInput
                    id="identifier"
                    v-model="form.identifier"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="name@example.com или +7 999 123-45-67"
                />
                <FieldHint :show="form.identifier.length > 0">
                    Если регистрировались по телефону, введите его в любом удобном формате.
                </FieldHint>
                <InputError class="mt-2" :message="form.errors.identifier" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Пароль" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <FieldHint :show="form.password.length > 0">
                    Пароль чувствителен к регистру и пробелам.
                </FieldHint>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-brand-muted">Запомнить меня</span>
                </label>
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-end gap-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-brand-muted underline hover:text-brand focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2"
                >
                    Забыли пароль?
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Войти
                </PrimaryButton>
            </div>
        </form>
    </AuthInputLayout>
</template>
