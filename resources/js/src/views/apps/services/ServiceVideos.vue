<template>
    <div class="p-6">
        <!-- Header con navegación -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <button 
                    type="button" 
                    class="btn btn-outline-primary btn-sm" 
                    @click="$router.back()"
                >
                    <svg class="w-4 h-4 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ $t('videos_page.back_btn') }}
                </button>
                <div>
                    <h2 class="text-2xl font-bold">{{ $t('videos_page.title') }}</h2>
                    <p class="text-gray-600">{{ service?.name }}</p>
                </div>
            </div>
            
            <button 
                v-if="authStore.can('create videos')"
                type="button" 
                class="btn btn-primary" 
                @click="editVideo()"
            >
                <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ $t('videos_page.add_video_btn') }}
            </button>
        </div>

        <!-- Estadísticas del servicio -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ videos.length }}</div>
                <div class="text-sm">{{ $t('videos_page.total_videos') }}</div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ activeVideosCount }}</div>
                <div class="text-sm">{{ $t('videos_page.active_videos') }}</div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ freeVideosCount }}</div>
                <div class="text-sm">{{ $t('videos_page.free_videos') }}</div>
            </div>
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ totalDuration }}</div>
                <div class="text-sm">{{ $t('videos_page.total_duration') }}</div>
            </div>
        </div>

        <!-- Alertas -->
        <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            {{ errorMessage }}
            <button type="button" class="btn-close" @click="errorMessage = ''"></button>
        </div>

        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ successMessage }}
            <button type="button" class="btn-close" @click="successMessage = ''"></button>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1">
                <input
                    type="text"
                    :placeholder="$t('videos_page.search_placeholder')"
                    class="form-input w-full"
                    v-model="searchVideo"
                />
            </div>
            <div class="flex gap-2">
                <select v-model="statusFilter" class="form-select">
                    <option value="">{{ $t('videos_page.all_statuses') }}</option>
                    <option value="active">{{ $t('videos_page.active') }}</option>
                    <option value="inactive">{{ $t('videos_page.inactive') }}</option>
                </select>
                <select v-model="freeFilter" class="form-select">
                    <option value="">{{ $t('videos_page.all_types') }}</option>
                    <option value="free">{{ $t('videos_page.free') }}</option>
                    <option value="paid">{{ $t('videos_page.paid') }}</option>
                </select>
            </div>
        </div>

        <!-- Lista de vídeos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <template v-if="loading">
                <div v-for="i in 6" :key="i" class="bg-white dark:bg-gray-800 rounded-lg shadow animate-pulse">
                    <div class="h-48 bg-gray-300 rounded-t-lg"></div>
                    <div class="p-4">
                        <div class="h-4 bg-gray-300 rounded mb-2"></div>
                        <div class="h-3 bg-gray-300 rounded w-3/4"></div>
                    </div>
                </div>
            </template>
            
            <template v-else>
                <div 
                    v-for="video in filteredVideos" 
                    :key="video.id"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow"
                >
                    <!-- Thumbnail del vídeo -->
                    <div class="relative">
                        <img 
                            :src="video.thumbnail_url || '/assets/images/video-placeholder.jpg'" 
                            :alt="video.title"
                            class="w-full h-48 object-cover rounded-t-lg"
                            @error="handleImageError"
                        />
                        <div class="absolute top-2 right-2 bg-black bg-opacity-70 text-white px-2 py-1 rounded text-sm">
                            {{ formatDuration(video.duration_seconds) }}
                        </div>
                        <div class="absolute bottom-2 left-2 flex gap-1">
                            <span v-if="video.is_free" class="badge bg-green-500 text-white">{{ $t('videos_page.free') }}</span>
                            <span v-if="!video.is_active" class="badge bg-red-500 text-white">{{ $t('videos_page.inactive') }}</span>
                        </div>
                    </div>
                    
                    <!-- Información del vídeo -->
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2 line-clamp-2">{{ video.title }}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ video.description }}</p>
                        
                        <div class="flex justify-between items-center text-sm text-gray-500 mb-3">
                            <span>{{ $t('videos_page.order') }}: {{ video.order }}</span>
                            <span>{{ formatDate(video.created_at) }}</span>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="flex gap-2">
                            <button 
                                type="button" 
                                class="btn btn-outline-primary btn-sm flex-1"
                                @click="previewVideo(video)"
                            >
                                <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $t('videos_page.view_btn') }}
                            </button>
                            
                            <button 
                                v-if="authStore.can('edit videos')"
                                type="button" 
                                class="btn btn-outline-secondary btn-sm"
                                @click="editVideo(video)"
                                :title="$t('videos_page.edit_tooltip')"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            
                            <button 
                                v-if="authStore.can('delete videos')"
                                type="button" 
                                class="btn btn-outline-danger btn-sm"
                                @click="deleteVideo(video)"
                                :title="$t('videos_page.delete_tooltip')"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div v-if="filteredVideos.length === 0" class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">{{ $t('videos_page.no_videos_title') }}</h3>
                    <p class="mt-1 text-gray-500">{{ $t('videos_page.no_videos_description') }}</p>
                    <button 
                        v-if="authStore.can('create videos')"
                        type="button" 
                        class="btn btn-primary mt-4"
                        @click="editVideo()"
                    >
                        {{ $t('videos_page.add_first_video_btn') }}
                    </button>
                </div>
            </template>
        </div>

        <div v-if="service?.whatsapp_url" class="flex justify-center mt-8 mb-4">
            <button 
                type="button" 
                class="btn btn-success btn-lg"
                @click="openWhatsApp"
            >
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893c0-3.18-1.24-6.169-3.495-8.418"/>
                </svg>
                <span class="ml-2">{{ $t('videos_page.whatsapp_btn') }}</span>
            </button>
        </div>

        <!-- Botón flotante de WhatsApp para móviles -->
        <div v-if="service?.whatsapp_url" class="fixed bottom-6 right-6 z-50 md:hidden">
            <button 
                type="button" 
                class="btn btn-success btn-circle shadow-lg"
                @click="openWhatsApp"
                :title="$t('videos_page.whatsapp_tooltip')"
            >
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893c0-3.18-1.24-6.169-3.495-8.418"/>
                </svg>
            </button>
        </div>

        <!-- Modal para agregar/editar vídeo -->
        <VideoModal 
            :show="showVideoModal"
            :video="editingVideo"
            :service-id="serviceId"
            @close="closeVideoModal"
            @saved="handleVideoSaved"
        />
        
        <!-- Modal para previsualizar vídeo -->
        <VideoPreviewModal 
            :show="showPreviewModal"
            :video="previewVideoData"
            @close="showPreviewModal = false"
        />
    </div>
</template>

<script lang="ts" setup>
    import { ref, computed, onMounted } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { useAuthStore } from '@/stores/auth';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import VideoModal from './components/VideoModal.vue';
    import VideoPreviewModal from './components/VideoPreviewModal.vue';
    import { useI18n } from 'vue-i18n';

    const { t } = useI18n();

    const route = useRoute();
    const router = useRouter();
    const authStore = useAuthStore();

    const serviceId = ref(route.params.serviceId);
    const service = ref<any>(null);
    const videos = ref<any[]>([]);
    const loading = ref(false);
    const errorMessage = ref('');
    const successMessage = ref('');

    // Filtros
    const searchVideo = ref('');
    const statusFilter = ref('');
    const freeFilter = ref('');

    // Modales
    const showVideoModal = ref(false);
    const showPreviewModal = ref(false);
    const editingVideo = ref<any>(null);
    const previewVideoData = ref<any>(null);

    // Computed properties
    const filteredVideos = computed(() => {
        return videos.value.filter(video => {
            // Validar que video y video.title existan antes de usar toLowerCase()
            const title = video?.title || '';
            const description = video?.description || '';
            const searchTerm = searchVideo.value.toLowerCase();
            
            const matchesSearch = title.toLowerCase().includes(searchTerm) ||
                                description.toLowerCase().includes(searchTerm);
            
            const matchesStatus = statusFilter.value === '' || 
                                (statusFilter.value === 'active' && video.is_active) ||
                                (statusFilter.value === 'inactive' && !video.is_active);
            
            const matchesFree = freeFilter.value === '' ||
                              (freeFilter.value === 'free' && video.is_free) ||
                              (freeFilter.value === 'paid' && !video.is_free);
            
            return matchesSearch && matchesStatus && matchesFree;
        });
    });

    const activeVideosCount = computed(() => {
        return videos.value.filter((v: any) => v.is_active).length;
    });

    const freeVideosCount = computed(() => {
        return videos.value.filter((v: any) => v.is_free).length;
    });

    const totalDuration = computed(() => {
        const totalSeconds = videos.value.reduce((sum: number, video: any) => sum + (video.duration_seconds || 0), 0);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        return `${hours}h ${minutes}m`;
    });

    onMounted(() => {
        fetchServiceAndVideos();
    });

    const fetchServiceAndVideos = async () => {
        loading.value = true;
        try {
            // Obtener servicio
            const serviceResponse = await axios.get(`/api/services/${serviceId.value}`);
            service.value = serviceResponse.data;
            
            // Los videos ya vienen incluidos en la respuesta del servicio
            videos.value = Array.isArray(serviceResponse.data.videos) 
                ? serviceResponse.data.videos 
                : [];

        } catch (error: any) {
            console.error('Error fetching service and videos:', error);
            errorMessage.value = error.response?.data?.message || 'Error al cargar los datos del servicio.';
            showMessage(errorMessage.value, 'error');
        } finally {
            loading.value = false;
        }
    };

    const formatDuration = (seconds: number): string => {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        
        if (hours > 0) {
            return `${hours}h ${minutes.toString().padStart(2, '0')}m ${secs.toString().padStart(2, '0')}s`;
        } else if (minutes > 0) {
            return `${minutes}m ${secs.toString().padStart(2, '0')}s`;
        } else {
            return `${secs}s`;
        }
    };

    const formatDate = (dateString?: string): string => {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    };

    const handleImageError = (event: Event) => {
        const target = event.target as HTMLImageElement;
        target.src = '/assets/images/video-placeholder.jpg';
    };

    const editVideo = (video: any = null) => {
        editingVideo.value = video;
        showVideoModal.value = true;
    };

    const previewVideo = (video: any) => {
        previewVideoData.value = video;
        showPreviewModal.value = true;
    };

    const deleteVideo = async (video: any) => {
        const result = await Swal.fire({
            title: t('videos_page.delete_confirm_title'),
            text: t('videos_page.delete_confirm_text'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: t('videos_page.delete_confirm_yes'),
            cancelButtonText: t('videos_page.delete_confirm_cancel')
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/videos/${video.id}`);
                videos.value = videos.value.filter((v: any) => v.id !== video.id);
                showMessage(t('videos_page.delete_success'), 'success');
            } catch (error: any) {
                console.error('Error deleting video:', error);
                showMessage(error.response?.data?.message || t('videos_page.delete_error'), 'error');
            }
        }
    };

    const closeVideoModal = () => {
        showVideoModal.value = false;
        editingVideo.value = null;
    };

    const handleVideoSaved = (video: any) => {

        const savedVideo = video.data;
        console.log('Video guardado:', savedVideo); // Debug
        
        if (editingVideo.value && savedVideo.id) {
            // Actualizar vídeo existente
            const index = videos.value.findIndex((v: any) => v.id === savedVideo.id);
            if (index !== -1) {
                videos.value.splice(index, 1, savedVideo);
            } else {
                // Si no existe, agregarlo (por si acaso)
                videos.value.unshift(savedVideo);
            }
        } else if (savedVideo.id) {
            // Agregar nuevo vídeo - asegurarse de que sea el primer elemento
            videos.value.unshift(savedVideo);
        }
        
        closeVideoModal();
        
        const message = editingVideo.value 
            ? t('videos_page.update_success') 
            : t('videos_page.create_success');
        showMessage(message, 'success');
    };

    const showMessage = (msg = '', type = 'success') => {
        if (type === 'success') {
            successMessage.value = msg;
        } else {
            errorMessage.value = msg;
        }
        
        const toast: any = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
            customClass: { container: 'toast' },
        });
        toast.fire({
            icon: type,
            title: msg,
            padding: '10px 20px',
        });
    };

    const openWhatsApp = () => {
        if (service.value?.whatsapp_url) {
            window.open(service.value.whatsapp_url, '_blank', 'noopener,noreferrer');
        } else {
            showMessage('URL de WhatsApp no disponible', 'error');
        }
    };
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.min-h-\[40px\] {
    min-height: 40px;
}

.from-green-500 {
    --tw-gradient-from: #3bf683 var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(59 130 246 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}

.from-purple-500 {
    --tw-gradient-from: #b53bf6 var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(59 130 246 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}

.from-orange-500 {
    --tw-gradient-from: #f6923b var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(59 130 246 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}


/* Estilos para el botón circular en móviles */
.btn-circle {
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

/* Animación para el botón flotante */
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.btn-circle:hover {
    animation: pulse 1s infinite;
}
</style>