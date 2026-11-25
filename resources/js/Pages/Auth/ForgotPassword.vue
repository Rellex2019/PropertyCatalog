<script setup>
import AuthNotice from '@/Components/AuthNotice.vue';
import FieldHint from '@/Components/FieldHint.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthInputLayout from '@/Layouts/AuthInputLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthInputLayout>
        <SeoHead
            title="Восстановление пароля"
            description="Запросите ссылку для восстановления пароля от аккаунта ДомГид."
            :noindex="true"
        />

        <h1 class="mb-4 text-2xl font-semibold text-brand">Восстановление пароля</h1>

        <p class="mb-4 text-sm text-brand-muted">
            Забыли пароль? Укажите email — отправим ссылку, по которой можно установить новый.
        </p>

        <AuthNotice v-if="status" :message="status" tone="success" class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <FieldHint :show="form.email.length > 0">
                    Письмо придёт на адрес, указанный при регистрации.
                </FieldHint>

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Отправить ссылку
                </PrimaryButton>
            </div>
        </form>
    </AuthInputLayout>
</template>
