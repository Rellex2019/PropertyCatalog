<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

defineProps({
    users: {
        type: Object,
        required: true,
    },
    counts: {
        type: Object,
        required: true,
    },
});

const roleLabel = (role) => ({
    manager: 'Менеджер',
    admin: 'Администратор',
    main_admin: 'Главный администратор',
}[role] || role);

const formatDate = (value) => value
    ? new Intl.DateTimeFormat('ru-RU', { dateStyle: 'medium' }).format(new Date(value))
    : '—';
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-brand">Пользователи</h1>
                <p class="mt-2 max-w-2xl text-sm text-brand-muted">
                    Управление аккаунтами сотрудников.
                </p>
            </div>
            <Link :href="route('admin.users.create')" class="site-btn-dark">
                Создать аккаунт
            </Link>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="site-card p-5">
                <p class="site-label">Менеджеров</p>
                <p class="mt-1 text-3xl font-semibold text-brand">{{ counts.managers }}</p>
            </div>
            <div class="site-card p-5">
                <p class="site-label">Администраторов</p>
                <p class="mt-1 text-3xl font-semibold text-brand">{{ counts.admins }}</p>
            </div>
        </div>

        <div class="site-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="border-b border-brand-line bg-brand-cream text-xs uppercase tracking-wider text-brand-soft">
                        <tr>
                            <th class="px-5 py-3">Имя</th>
                            <th class="px-5 py-3">Контакт</th>
                            <th class="px-5 py-3">Роль</th>
                            <th class="px-5 py-3">Создан</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-line">
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="px-5 py-4 font-medium text-brand">{{ user.name }}</td>
                            <td class="px-5 py-4 text-brand-muted">
                                <span>{{ user.email }}</span>
                                <span v-if="user.phone" class="block text-xs">{{ user.phone }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="site-badge" :class="user.role === 'admin' ? 'bg-brand text-white' : 'bg-brand-sand text-brand'">
                                    {{ roleLabel(user.role) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-brand-muted">{{ formatDate(user.created_at) }}</td>
                        </tr>
                        <tr v-if="!users.data.length">
                            <td colspan="4" class="px-5 py-8 text-center text-brand-muted">
                                Сотрудников пока нет.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="users.links?.length > 1" class="flex flex-wrap gap-2 border-t border-brand-line p-4">
                <Link
                    v-for="(link, index) in users.links"
                    :key="index"
                    :href="link.url || '#'"
                    class="site-btn-outline px-3 py-2 text-xs"
                    :class="link.active ? 'bg-brand text-white' : ''"
                    :aria-current="link.active ? 'page' : undefined"
                    v-html="link.label"
                />
            </div>
        </div>
    </div>
</template>
