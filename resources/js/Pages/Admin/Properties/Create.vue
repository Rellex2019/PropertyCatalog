<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

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
const imagePreview = ref(null);
const imagesPreview = ref([]);

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
            <h1 class="text-2xl font-bold text-gray-900">Добавление объекта</h1>
            <Link 
                :href="route('admin.properties.index')"
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
            >
                Назад к списку
            </Link>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form @submit.prevent="submit" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Название -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Название *</label>
                        <input 
                            v-model="form.title"
                            type="text"
                            required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                    </div>

                    <!-- Описание -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Описание</label>
                        <textarea 
                            v-model="form.description"
                            rows="4"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        ></textarea>
                    </div>

                    <!-- Цена -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Цена *</label>
                        <input 
                            v-model="form.price"
                            type="number"
                            required
                            min="0"
                            step="0.01"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                    </div>

                    <!-- Валюта -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Валюта</label>
                        <select 
                            v-model="form.currency"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        >
                            <option value="₽">₽</option>
                            <option value="$">$</option>
                            <option value="€">€</option>
                        </select>
                    </div>

                    <!-- Адрес -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Адрес *</label>
                        <input 
                            v-model="form.address"
                            type="text"
                            required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                    </div>

                    <!-- Город -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Город *</label>
                        <input 
                            v-model="form.city"
                            type="text"
                            required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                    </div>

                    <!-- Площадь -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Площадь (м²) *</label>
                        <input 
                            v-model="form.area"
                            type="number"
                            required
                            min="0"
                            step="0.1"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                    </div>

                    <!-- Комнаты -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Комнат *</label>
                        <input 
                            v-model="form.rooms"
                            type="number"
                            required
                            min="0"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                    </div>

                    <!-- Этаж -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Этаж *</label>
                        <input 
                            v-model="form.floor"
                            type="number"
                            required
                            min="0"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                    </div>

                    <!-- Всего этажей -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Всего этажей *</label>
                        <input 
                            v-model="form.total_floors"
                            type="number"
                            required
                            min="1"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                    </div>

                    <!-- Тип -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Тип *</label>
                        <select 
                            v-model="form.type"
                            required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        >
                            <option value="">Выберите тип</option>
                            <option 
                                v-for="type in props.types" 
                                :key="type" 
                                :value="type"
                            >
                                {{ type }}
                            </option>
                        </select>
                    </div>

                    <!-- Статус -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Статус *</label>
                        <select 
                            v-model="form.status"
                            required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        >
                            <option 
                                v-for="status in props.statuses" 
                                :key="status" 
                                :value="status"
                            >
                                {{ status }}
                            </option>
                        </select>
                    </div>

                    <!-- Удобства -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Удобства</label>
                        <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                            <label 
                                v-for="amenity in props.amenitiesList" 
                                :key="amenity" 
                                class="flex items-center"
                            >
                                <input 
                                    v-model="form.amenities"
                                    type="checkbox"
                                    :value="amenity"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                                />
                                <span class="ml-2 text-sm text-gray-700">{{ amenity }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Чекбоксы -->
                    <div class="col-span-2 flex flex-wrap gap-6">
                        <label class="flex items-center">
                            <input 
                                v-model="form.is_featured"
                                type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                            />
                            <span class="ml-2 text-sm text-gray-700">Избранное</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                            />
                            <span class="ml-2 text-sm text-gray-700">Активен</span>
                        </label>
                    </div>

                    <!-- Дата публикации -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Дата публикации</label>
                        <input 
                            v-model="form.published_at"
                            type="date"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        />
                    </div>

                    <!-- Главное изображение -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Главное изображение</label>
                        <input 
                            type="file"
                            accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                            @change="handleImageUpload"
                        />
                        <div v-if="imagePreview" class="mt-2">
                            <img :src="imagePreview" class="w-32 h-32 object-cover rounded border" />
                        </div>
                    </div>

                    <!-- Дополнительные изображения -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Дополнительные изображения</label>
                        <input 
                            type="file"
                            accept="image/*"
                            multiple
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                            @change="handleImagesUpload"
                        />
                        <div v-if="imagesPreview.length" class="mt-2 flex flex-wrap gap-2">
                            <div 
                                v-for="(preview, index) in imagesPreview" 
                                :key="index"
                                class="relative group"
                            >
                                <img 
                                    :src="preview" 
                                    class="w-20 h-20 object-cover rounded border"
                                />
                                <button 
                                    @click="removeImage(index)"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                    type="button"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex justify-end gap-4 pt-6 mt-6 border-t border-gray-200">
                    <Link 
                        :href="route('admin.properties.index')"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                    >
                        Отмена
                    </Link>
                    <button 
                        type="submit"
                        :disabled="submitting"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg v-if="submitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ submitting ? 'Сохранение...' : 'Сохранить' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>