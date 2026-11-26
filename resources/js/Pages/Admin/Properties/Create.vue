<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FieldHint from '@/Components/FieldHint.vue';

defineOptions({
    layout: AdminLayout
});

const props = defineProps({
    types: Array,
    statuses: Array,
    amenitiesList: Array
});

// Форма
const form = reactive({
    title: '',
    description: '',
    price: '',
    currency: '₽',
    address: '',
    city: '',
    area: '',
    rooms: '',
    floor: '',
    total_floors: '',
    type: '',
    status: 'Продается',
    amenities: [],
    is_featured: false,
    is_active: true,
    published_at: new Date().toISOString().split('T')[0],
    image: null,
    images: []
});

const submitting = ref(false);
const errors = ref({});
const amenitiesInput = ref('');
const imagePreview = ref(null);
const imagesPreview = ref([]);

const checkFormating = (event) => {
    const value = event.target.value;

    const regex = /^[\w\s\-а-яА-ЯёЁ]+\s*(,\s*[\w\s\-а-яА-ЯёЁ]+\s*)*$/u;

    if (regex.test(value)) {
        errors.value.amenities = '';
        let result = form.amenities = normalizeAmenities(value);
        console.log(result)
    } else {
        errors.value.amenities = "Используйте только слова, запятые и пробелы между ними";
    }
}

function normalizeAmenities(input) {
    if (!input || !input.trim()) return '';
    let items = input.split(',');
    items = items.map(item => item.trim());
    items = items.filter(item => item.length > 0);
    return items;
}

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const handleImagesUpload = (event) => {
    const files = event.target.files;
    if (files) {
        form.images = Array.from(files);
        imagesPreview.value = [];
        Array.from(files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagesPreview.value.push(e.target.result);
            };
            reader.readAsDataURL(file);
        });
    }
};

const removeImage = (index) => {
    form.images.splice(index, 1);
    imagesPreview.value.splice(index, 1);
};

const submit = () => {
    submitting.value = true;
    errors.value = {};

    const formData = new FormData();

    Object.keys(form).forEach(key => {
        if (key === 'image' && form[key] instanceof File) {
            formData.append('image', form[key]);
        } else if (key === 'images' && Array.isArray(form[key])) {
            form[key].forEach((file, index) => {
                if (file instanceof File) {
                    formData.append(`images[${index}]`, file);
                }
            });
        } else if (key === 'amenities' && Array.isArray(form[key])) {
            form[key].forEach((item, index) => {
                formData.append(`amenities[${index}]`, item);
            });
        } else if (key !== 'images' && key !== 'image') {
            formData.append(key, form[key] ?? '');
        }
    });

    router.post('/admin/properties', formData, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            submitting.value = false;
        },
        onError: (err) => {
            errors.value = err;
            submitting.value = false;
        }
    });
};
</script>

<template>
    <div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-brand">Добавление объекта</h1>
            <Link :href="route('admin.properties.index')"
                class="site-btn-outline"
>
                Назад к списку
            </Link>
        </div>

        <div class="site-card p-6">
            <form @submit.prevent="submit" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Название -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-brand-ink">Название *</label>
                        <input v-model="form.title" type="text" required
                            class="site-input mt-1"
                            placeholder="Например, 2-к. квартира на Тверской"
 />
                        <FieldHint :show="form.title.length > 0">
                            Коротко и понятно — это заголовок в каталоге.
                        </FieldHint>
                    </div>

                    <!-- Описание -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-brand-ink">Описание</label>
                        <textarea v-model="form.description" rows="4"
                            class="site-input mt-1"
                            placeholder="Опишите планировку, состояние и близость к инфраструктуре"
></textarea>
                        <FieldHint :show="form.description.length > 0">
                            Пара предложений о главном — этого достаточно.
                        </FieldHint>
                    </div>

                    <!-- Цена -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Цена *</label>
                        <input v-model="form.price" type="number" required min="0" step="0.01"
                            class="site-input mt-1"
                            placeholder="Например, 12500000"
 />
                        <FieldHint :show="form.price.length > 0">
                            Укажите полную стоимость без пробелов.
                        </FieldHint>
                    </div>

                    <!-- Валюта -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Валюта</label>
                        <select v-model="form.currency"
                            class="site-input mt-1"
>
                            <option value="₽">₽</option>
                            <option value="$">$</option>
                            <option value="€">€</option>
                        </select>
                        <FieldHint :show="true">
                            Валюта, в которой указана цена.
                        </FieldHint>
                    </div>

                    <!-- Адрес -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Адрес *</label>
                        <input v-model="form.address" type="text" required
                            class="site-input mt-1"
                            placeholder="Улица, дом"
 />
                        <FieldHint :show="form.address.length > 0">
                            Улица и номер дома.
                        </FieldHint>
                    </div>

                    <!-- Город -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Город *</label>
                        <input v-model="form.city" type="text" required
                            class="site-input mt-1"
                            placeholder="Например, Москва"
 />
                        <FieldHint :show="form.city.length > 0">
                            Город используется в фильтре каталога.
                        </FieldHint>
                    </div>

                    <!-- Площадь -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Площадь (м²) *</label>
                        <input v-model="form.area" type="number" required min="0" step="0.1"
                            class="site-input mt-1"
                            placeholder="Например, 54.5"
 />
                        <FieldHint :show="form.area.length > 0">
                            Площадь в квадратных метрах.
                        </FieldHint>
                    </div>

                    <!-- Комнаты -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Комнат *</label>
                        <input v-model="form.rooms" type="number" required min="0"
                            class="site-input mt-1"
                            placeholder="Например, 2"
 />
                        <FieldHint :show="form.rooms.length > 0">
                            Для студии укажите 0.
                        </FieldHint>
                    </div>

                    <!-- Этаж -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Этаж *</label>
                        <input v-model="form.floor" type="number" required min="0"
                            class="site-input mt-1"
                            placeholder="Например, 5"
 />
                        <FieldHint :show="form.floor.length > 0">
                            Этаж квартиры в здании.
                        </FieldHint>
                    </div>

                    <!-- Всего этажей -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Всего этажей *</label>
                        <input v-model="form.total_floors" type="number" required min="1"
                            class="site-input mt-1"
                            placeholder="Например, 12"
 />
                        <FieldHint :show="form.total_floors.length > 0">
                            Общее число этажей в доме.
                        </FieldHint>
                    </div>

                    <!-- Тип -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Тип *</label>
                        <select v-model="form.type" required
                            class="site-input mt-1"
>
                            <option value="">Выберите тип</option>
                            <option v-for="type in props.types" :key="type" :value="type">
                                {{ type }}
                            </option>
                        </select>
                        <FieldHint :show="form.type.length > 0">
                            Например, квартира или дом.
                        </FieldHint>
                    </div>

                    <!-- Статус -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Статус *</label>
                        <select v-model="form.status" required
                            class="site-input mt-1"
>
                            <option v-for="status in props.statuses" :key="status" :value="status">
                                {{ status }}
                            </option>
                        </select>
                        <FieldHint :show="form.status.length > 0">
                            Статус продажи объекта.
                        </FieldHint>
                    </div>

                    <!-- Удобства -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-brand-ink">Удобства</label>
                        <input v-model="amenitiesInput" type="text" @input="checkFormating"
                            class="site-input mt-1"
                            placeholder="Парковка, лифт, балкон"
>
                        <FieldHint :show="amenitiesInput.length > 0">
                            Перечислите удобства через запятую.
                        </FieldHint>
                        <div v-if="errors.amenities" class="mt-1 text-sm text-red-600">
                            {{ errors.amenities }}
                        </div>
                    </div>

                    <!-- Чекбоксы -->
                    <div class="col-span-2 flex flex-wrap gap-6">
                        <label class="flex items-center">
                            <input v-model="form.is_featured" type="checkbox"
                                class="rounded border-brand-line text-brand shadow-sm focus:border-brand-gold focus:ring-brand-gold" />
                            <span class="ml-2 text-sm text-brand-ink">Избранное</span>
                        </label>
                        <label class="flex items-center">
                            <input v-model="form.is_active" type="checkbox"
                                class="rounded border-brand-line text-brand shadow-sm focus:border-brand-gold focus:ring-brand-gold" />
                            <span class="ml-2 text-sm text-brand-ink">Активен</span>
                        </label>
                    </div>

                    <!-- Дата публикации -->
                    <div>
                        <label class="block text-sm font-medium text-brand-ink">Дата публикации</label>
                        <input v-model="form.published_at" type="date"
                            class="site-input mt-1"
                            placeholder="ГГГГ-ММ-ДД"
 />
                        <FieldHint :show="form.published_at.length > 0">
                            Дата появления объекта в каталоге.
                        </FieldHint>
                    </div>

                    <!-- Главное изображение -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-brand-ink">Главное изображение</label>
                        <input type="file" accept="image/*"
                            class="mt-1 block w-full text-sm text-brand-muted file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-sand file:text-brand hover:file:bg-brand-cream"
                            @change="handleImageUpload" />
                        <FieldHint :show="true">
                            JPG или PNG — это изображение станет обложкой в каталоге.
                        </FieldHint>
                        <div v-if="imagePreview" class="mt-2">
                            <img :src="imagePreview" class="w-32 h-32 object-cover rounded border" />
                        </div>
                    </div>

                    <!-- Дополнительные изображения -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-brand-ink">Дополнительные изображения</label>
                        <input type="file" accept="image/*" multiple
                            class="mt-1 block w-full text-sm text-brand-muted file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-sand file:text-brand hover:file:bg-brand-cream"
                            @change="handleImagesUpload" />
                        <FieldHint :show="true">
                            Можно выбрать несколько — они появятся в галерее.
                        </FieldHint>
                        <div v-if="imagesPreview.length" class="mt-2 flex flex-wrap gap-2">
                            <div v-for="(preview, index) in imagesPreview" :key="index" class="relative group">
                                <img :src="preview" class="w-20 h-20 object-cover rounded border" />
                                <button @click="removeImage(index)"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                    type="button">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex justify-end gap-4 pt-6 mt-6 border-t border-brand-line">
                    <Link :href="route('admin.properties.index')"
                        class="site-btn-outline"
>
                        Отмена
                    </Link>
                    <button type="submit" :disabled="submitting"
                        class="px-4 py-2 bg-brand text-white rounded-xl hover:bg-brand-forest disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg v-if="submitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ submitting ? 'Сохранение...' : 'Сохранить' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>