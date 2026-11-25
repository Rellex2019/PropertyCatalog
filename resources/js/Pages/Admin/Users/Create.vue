<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FieldHint from '@/Components/FieldHint.vue';
import InputError from '@/Components/InputError.vue';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    roles: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    role: '',
    password: '',
    password_confirmation: '',
});

const passwordChecks = computed(() => [
    { label: 'минимум 12 символов', ok: form.password.length >= 12 },
    {
        label: 'заглавная и строчная буквы',
        ok: /\p{Ll}/u.test(form.password) && /\p{Lu}/u.test(form.password),
    },
    { label: 'хотя бы одна цифра', ok: /\d/u.test(form.password) },
    {
        label: 'хотя бы один специальный символ',
        ok: /[^\p{L}\p{N}]/u.test(form.password),
    },
]);

const passwordErrorIsList = computed(() => String(form.errors.password ?? '')
    .startsWith('Пароль не соответствует требованиям:'));

const passwordProblems = computed(() => passwordErrorIsList.value
    ? String(form.errors.password)
        .replace(/^Пароль не соответствует требованиям: /, '')
        .replace(/\.$/, '')
        .split('; ')
    : []);

const submit = () => {
    form.post(route('admin.users.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-brand">Создание аккаунта сотрудника</h1>
                <p class="mt-2 max-w-2xl text-sm text-brand-muted">
                    Создайте аккаунт менеджера или администратора.
                </p>
            </div>
            <Link :href="route('admin.users.index')" class="site-btn-outline">
                Назад к списку
            </Link>
        </div>

        <div class="site-card p-5 sm:p-8">
            <div class="mb-6 rounded-2xl bg-brand-cream p-4 text-sm text-brand-ink">
                <p class="font-semibold">Требования к паролю сотрудника</p>
                <ul class="mt-2 space-y-1 text-xs">
                    <li
                        v-for="check in passwordChecks"
                        :key="check.label"
                        class="flex items-center gap-2"
                        :class="check.ok ? 'text-brand' : 'text-brand-soft'"
                    >
                        <svg
                            v-if="check.ok"
                            class="h-3.5 w-3.5 shrink-0 text-brand"
                            fill="none"
                            viewBox="0 0 20 20"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 10 4 4 6-7" />
                        </svg>
                        <span v-else class="h-1.5 w-1.5 shrink-0 rounded-full bg-brand-line"></span>
                        {{ check.label }}
                    </li>
                </ul>
            </div>

            <form class="grid gap-5 md:grid-cols-2" @submit.prevent="submit">
                <div class="md:col-span-2">
                    <label class="site-label" for="name">Имя *</label>
                    <input id="name" v-model="form.name" type="text" class="site-input mt-1" required autocomplete="name" placeholder="Например, Мария Менеджер" />
                    <FieldHint :show="form.name.length > 0">
                        Имя будет видно в списке сотрудников.
                    </FieldHint>
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <label class="site-label" for="email">Email *</label>
                    <input id="email" v-model="form.email" type="email" class="site-input mt-1" required autocomplete="email" placeholder="manager@example.com" />
                    <FieldHint :show="form.email.length > 0">
                        Нужен для входа и восстановления пароля.
                    </FieldHint>
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <label class="site-label" for="phone">Телефон (необязательно)</label>
                    <input id="phone" v-model="form.phone" type="tel" class="site-input mt-1" autocomplete="tel" placeholder="+7 999 123-45-67" />
                    <FieldHint :show="form.phone.length > 0">
                        От 7 до 15 цифр; можно с +, пробелами и скобками.
                    </FieldHint>
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <div class="md:col-span-2">
                    <label class="site-label" for="role">Роль *</label>
                    <select id="role" v-model="form.role" class="site-input mt-1" required>
                        <option value="" disabled>Выберите роль</option>
                        <option v-for="role in roles" :key="role.value" :value="role.value">
                            {{ role.label }}
                        </option>
                    </select>
                    <FieldHint :show="form.role.length > 0">
                        Менеджер или администратор.
                    </FieldHint>
                    <InputError class="mt-2" :message="form.errors.role" />
                </div>

                <div>
                    <label class="site-label" for="password">Пароль *</label>
                    <input id="password" v-model="form.password" type="password" class="site-input mt-1" required autocomplete="new-password" placeholder="Например, Kvartira2026!" />
                    <FieldHint :show="form.password.length > 0">
                        Проверьте требования к паролю выше.
                    </FieldHint>
                    <InputError
                        v-if="form.errors.password && !passwordErrorIsList"
                        class="mt-2"
                        :message="form.errors.password"
                    />
                    <div
                        v-if="passwordErrorIsList"
                        class="mt-2 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                        role="alert"
                    >
                        <p class="font-medium">Пароль не соответствует требованиям:</p>
                        <ul class="mt-1 list-inside list-disc space-y-1">
                            <li v-for="(problem, index) in passwordProblems" :key="index">
                                {{ problem }}
                            </li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label class="site-label" for="password_confirmation">Повторите пароль *</label>
                    <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="site-input mt-1" required autocomplete="new-password" placeholder="Тот же пароль" />
                    <FieldHint :show="form.password_confirmation.length > 0">
                        Введите тот же пароль без лишних пробелов.
                    </FieldHint>
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="md:col-span-2 flex flex-wrap justify-end gap-3 border-t border-brand-line pt-5">
                    <Link :href="route('admin.users.index')" class="site-btn-outline">
                        Отмена
                    </Link>
                    <button type="submit" class="site-btn-dark" :disabled="form.processing">
                        Создать аккаунт
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
