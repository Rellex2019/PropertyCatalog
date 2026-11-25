<script setup>
import FieldHint from '@/Components/FieldHint.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import AuthInputLayout from '@/Layouts/AuthInputLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <AuthInputLayout>
        <SeoHead
            title="Подтверждение пароля"
            description="Подтвердите пароль для доступа к защищённому разделу ДомГид."
            :noindex="true"
        />

        <h1 class="mb-4 text-2xl font-semibold text-brand">Подтверждение пароля</h1>

        <p class="mb-4 text-sm text-brand-muted">
            Это защищённый раздел. Введите пароль, чтобы продолжить.
        </p>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Пароль" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                />
                <FieldHint :show="form.password.length > 0">
                    Введите пароль от своего аккаунта.
                </FieldHint>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Подтвердить
                </PrimaryButton>
            </div>
        </form>
    </AuthInputLayout>
</template>
