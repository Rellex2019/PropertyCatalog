<script setup>
import AuthNotice from '@/Components/AuthNotice.vue';
import FieldHint from '@/Components/FieldHint.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthInputLayout from '@/Layouts/AuthInputLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthInputLayout>
        <SeoHead
            title="Регистрация"
            description="Создайте аккаунт ДомГид, чтобы сохранять понравившиеся объекты недвижимости."
            :noindex="true"
        />

        <h1 class="text-2xl font-semibold text-brand">Регистрация</h1>
        <p class="mt-3 max-w-xl text-sm text-brand-muted">
            Укажите email или телефон и придумайте пароль.
        </p>

        <form class="mt-6" @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Имя" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Например, Анна"
                />
                <FieldHint :show="form.name.length > 0">
                    Можно указать любое удобное имя.
                </FieldHint>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    autocomplete="username"
                    placeholder="name@example.com"
                />
                <FieldHint :show="form.email.length > 0">
                    Введите адрес полностью: с @ и точкой.
                </FieldHint>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone" value="Телефон" />
                <TextInput
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="mt-1 block w-full"
                    autocomplete="tel"
                    placeholder="+7 999 123-45-67"
                />
                <FieldHint :show="form.phone.length > 0">
                    Можно с +, пробелами и скобками.
                </FieldHint>
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <AuthNotice
                v-if="form.errors.email && form.errors.phone && form.errors.email === form.errors.phone"
                :message="form.errors.email"
                tone="error"
                class="mt-4"
            />

            <div class="mt-4">
                <InputLabel for="password" value="Пароль" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                    placeholder="Например, 1234"
                />
                <FieldHint :show="form.password.length > 0">
                    Минимум 4 символа.
                </FieldHint>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Повторите пароль" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                    placeholder="Тот же пароль ещё раз"
                />
                <FieldHint :show="form.password_confirmation.length > 0">
                    Повторите тот же пароль.
                </FieldHint>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-end gap-4">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-brand-muted underline hover:text-brand focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2"
                >
                    Уже есть аккаунт? Войти
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Зарегистрироваться
                </PrimaryButton>
            </div>
        </form>
    </AuthInputLayout>
</template>
