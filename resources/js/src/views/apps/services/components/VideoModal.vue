<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-[51]">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <DialogOverlay class="fixed inset-0 bg-[black]/60" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center px-4 py-8">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-2xl text-black dark:text-white-dark">
                            <button
                                type="button"
                                class="absolute top-4 ltr:right-4 rtl:left-4 text-gray-400 hover:text-gray-800 dark:hover:text-gray-600 outline-none"
                                @click="closeModal"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24px"
                                    height="24px"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="w-6 h-6"
                                >
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                            <div class="text-lg font-medium bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3 ltr:pr-[50px] rtl:pl-[50px]">
                                {{ video ? $t('video_modal.edit_video') : $t('video_modal.add_video') }}
                            </div>
                            <div class="p-5">
                                <form @submit.prevent="saveVideo">
                                    <div class="mb-5">
                                        <label for="title">{{ $t('video_modal.video_title') }} *</label>
                                        <input 
                                            id="title" 
                                            type="text" 
                                            :placeholder="$t('video_modal.title_placeholder')" 
                                            class="form-input" 
                                            v-model="form.title" 
                                            required 
                                        />
                                        <div v-if="errors.title" class="text-danger mt-1">{{ errors.title[0] }}</div>
                                    </div>

                                    <div class="mb-5">
                                        <label for="slug">{{ $t('video_modal.slug') }} *</label>
                                        <input 
                                            id="slug" 
                                            type="text" 
                                            :placeholder="$t('video_modal.slug_placeholder')" 
                                            class="form-input" 
                                            v-model="form.slug" 
                                            required 
                                        />
                                        <div class="text-xs text-gray-500 mt-1">{{ $t('video_modal.slug_helper') }}</div>
                                        <div v-if="errors.slug" class="text-danger mt-1">{{ errors.slug[0] }}</div>
                                    </div>

                                    <div class="mb-5">
                                        <label for="description">{{ $t('video_modal.description') }}</label>
                                        <textarea 
                                            id="description" 
                                            :placeholder="$t('video_modal.description_placeholder')" 
                                            class="form-textarea min-h-[100px]" 
                                            v-model="form.description"
                                        ></textarea>
                                        <div v-if="errors.description" class="text-danger mt-1">{{ errors.description[0] }}</div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                                        <div>
                                            <label for="video_url">{{ $t('video_modal.video_url') }} *</label>
                                            <input 
                                                id="video_url" 
                                                type="url" 
                                                :placeholder="$t('video_modal.video_url_placeholder')" 
                                                class="form-input" 
                                                v-model="form.video_url" 
                                                required 
                                            />
                                            <div class="text-xs text-gray-500 mt-1">{{ $t('video_modal.video_url_helper') }}</div>
                                            <div v-if="errors.video_url" class="text-danger mt-1">{{ errors.video_url[0] }}</div>
                                        </div>

                                        <div>
                                            <label for="thumbnail_url">{{ $t('video_modal.thumbnail_url') }}</label>
                                            <input 
                                                id="thumbnail_url" 
                                                type="url" 
                                                :placeholder="$t('video_modal.thumbnail_url_placeholder')" 
                                                class="form-input" 
                                                v-model="form.thumbnail_url" 
                                            />
                                            <div v-if="errors.thumbnail_url" class="text-danger mt-1">{{ errors.thumbnail_url[0] }}</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                                        <div>
                                            <label for="duration_seconds">{{ $t('video_modal.duration_seconds') }}</label>
                                            <input 
                                                id="duration_seconds" 
                                                type="number" 
                                                min="0" 
                                                :placeholder="$t('video_modal.duration_placeholder')" 
                                                class="form-input" 
                                                v-model="form.duration_seconds" 
                                                required 
                                            />
                                            <div class="text-xs text-gray-500 mt-1" v-if="form.duration_seconds">
                                                {{ $t('video_modal.duration_helper')}} {{ formatDuration(form.duration_seconds)}}
                                            </div>
                                            <div v-if="errors.duration_seconds" class="text-danger mt-1">{{ errors.duration_seconds[0] }}</div>
                                        </div>

                                        <div>
                                            <label for="order">{{ $t('video_modal.order') }}</label>
                                            <input 
                                                id="order" 
                                                type="number" 
                                                min="0" 
                                                :placeholder="$t('video_modal.order_placeholder')" 
                                                class="form-input" 
                                                v-model="form.order" 
                                            />
                                            <div v-if="errors.order" class="text-danger mt-1">{{ errors.order[0] }}</div>
                                        </div>

                                        <div>
                                            <label for="tags">{{ $t('video_modal.tags') }}</label>
                                            <input 
                                                id="tags" 
                                                type="text" 
                                                :placeholder="$t('video_modal.tags_placeholder')" 
                                                class="form-input" 
                                                v-model="tagsInput" 
                                            />
                                            <div class="text-xs text-gray-500 mt-1">{{ $t('video_modal.tags_helper') }}</div>
                                            <div v-if="errors.tags" class="text-danger mt-1">{{ errors.tags[0] }}</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                                        <div>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="checkbox" class="form-checkbox" v-model="form.is_free" />
                                                <span class="text-white-dark ltr:ml-3 rtl:mr-3">{{ $t('video_modal.is_free') }}</span>
                                            </label>
                                            <div class="text-xs text-gray-500 mt-1">{{ $t('video_modal.is_free_helper') }}</div>
                                        </div>

                                        <div>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="checkbox" class="form-checkbox" v-model="form.is_active" />
                                                <span class="text-white-dark ltr:ml-3 rtl:mr-3">{{ $t('video_modal.is_active') }}</span>
                                            </label>
                                            <div class="text-xs text-gray-500 mt-1">{{ $t('video_modal.is_active_helper') }}</div>
                                        </div>
                                    </div>

                                    <!-- Vista previa de la miniatura -->
                                    <div v-if="form.thumbnail_url" class="mb-5">
                                        <label class="block mb-2">{{ $t('video_modal.thumbnail_preview') }}</label>
                                        <img 
                                            :src="form.thumbnail_url" 
                                            alt="Vista previa" 
                                            class="max-w-full h-32 object-cover rounded border"
                                            @error="handleImageError"
                                        />
                                    </div>

                                    <div class="flex justify-end items-center mt-8">
                                        <button type="button" class="btn btn-outline-danger" @click="closeModal" :disabled="saving">
                                            {{ $t('video_modal.cancel_btn') }}
                                        </button>
                                        <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4" :disabled="saving">
                                            <span v-if="saving" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4 ltr:mr-2 rtl:ml-2 inline-block"></span>
                                            {{ video ? $t('video_modal.update_btn') : $t('video_modal.add_btn') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script lang="ts" setup>
    import { ref, watch, computed } from 'vue';
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import { useI18n } from 'vue-i18n';

    const { t } = useI18n();

    interface Video {
        id?: number;
        title: string;
        slug: string;
        description: string;
        video_url: string;
        thumbnail_url: string;
        duration_seconds: number;
        order: number;
        is_free: boolean;
        is_active: boolean;
        tags: string[];
    }

    interface Props {
        show: boolean;
        video: Video | null;
        serviceId: string | number;
    }

    interface Emits {
        (e: 'close'): void;
        (e: 'saved', video: Video): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    const saving = ref(false);
    const errors = ref<Record<string, string[]>>({});
    const tagsInput = ref('');

    const defaultForm: Video = {
        title: '',
        slug: '',
        description: '',
        video_url: '',
        thumbnail_url: '',
        duration_seconds: 0,
        order: 0,
        is_free: false,
        is_active: true,
        tags: []
    };

    const form = ref<Video>({ ...defaultForm });

    // Computed para generar slug automáticamente
    const autoSlug = computed(() => {
        return form.value.title
            .toLowerCase()
            .replace(/[^\w\s]/gi, '')
            .replace(/\s+/g, '-')
            .substring(0, 50);
    });

    // Watch para generar slug automático cuando cambia el título
    watch(() => form.value.title, (newTitle) => {
        if (newTitle && !form.value.slug) {
            form.value.slug = autoSlug.value;
        }
    });

    // Watch para sincronizar tags con el input
    watch(tagsInput, (newTags) => {
        if (newTags) {
            form.value.tags = newTags.split(',').map(tag => tag.trim()).filter(tag => tag);
        } else {
            form.value.tags = [];
        }
    });

    // Watch para cargar datos del vídeo cuando se abre el modal
    watch(() => props.video, (newVideo) => {
        if (newVideo) {
            form.value = { ...newVideo };
            tagsInput.value = Array.isArray(newVideo.tags) ? newVideo.tags.join(', ') : '';
        } else {
            //resetForm();
        }
    }, { immediate: true });

    const resetForm = () => {
        form.value = { ...defaultForm };
        tagsInput.value = '';
        errors.value = {};
    };

    const formatDuration = (seconds: number): string => {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        
        if (hours > 0) {
            return `${hours}h ${minutes}m ${secs}s`;
        } else if (minutes > 0) {
            return `${minutes}m ${secs}s`;
        } else {
            return `${secs}s`;
        }
    };

    const handleImageError = (event: Event) => {
        const target = event.target as HTMLImageElement;
        target.style.display = 'none';
    };

    const closeModal = () => {
        if (!saving.value) {
            resetForm();
            emit('close');
        }
    };

    const saveVideo = async () => {
        saving.value = true;
        errors.value = {};

        try {
            const payload = {
                ...form.value,
                service_id: props.serviceId
            };

            let response;
            if (props.video?.id) {
                // Actualizar vídeo existente
                response = await axios.put(`/api/videos/${props.video.id}`, payload);
            } else {
                // Crear nuevo vídeo
                response = await axios.post('/api/videos', payload);
            }

            emit('saved', response.data);
            
            Swal.fire({
                icon: 'success',
                title: t('video_modal.success_title'),
                text: props.video?.id ? t('video_modal.success_edit') : t('video_modal.success_add'),
                timer: 2000,
                showConfirmButton: false
            });

        } catch (error: any) {
            if (error.response?.status === 422) {
                errors.value = error.response.data.errors;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: t('video_modal.error_title'),
                    text: error.response?.data?.message || t('video_modal.error_message'),
                });
            }
        } finally {
            saving.value = false;
        }
    };
</script>