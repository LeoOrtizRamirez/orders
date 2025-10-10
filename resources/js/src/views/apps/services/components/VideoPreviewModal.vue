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
                        <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-4xl text-black dark:text-white-dark">
                            <button
                                type="button"
                                class="absolute top-4 ltr:right-4 rtl:left-4 text-gray-400 hover:text-gray-800 dark:hover:text-gray-600 outline-none z-10 bg-white dark:bg-gray-800 rounded-full p-1"
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
                                    class="w-5 h-5"
                                >
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                            
                            <div class="text-lg font-medium bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3 ltr:pr-[50px] rtl:pl-[50px]">
                                {{ video?.title }}
                            </div>
                            
                            <div class="p-5">
                                <!-- Video player -->
                                <div class="mb-6">
                                    <div class="relative bg-black rounded-lg overflow-hidden">
                                        <!-- Placeholder if no video URL -->
                                        <div v-if="!video?.video_url" class="aspect-video flex items-center justify-center bg-gray-800">
                                            <div class="text-center text-white">
                                                <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                <p>{{ $t('video_preview.video_unavailable') }}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- YouTube player -->
                                        <div v-else-if="isYouTubeUrl" class="aspect-video">
                                            <iframe 
                                                :src="youtubeEmbedUrl"
                                                class="w-full h-full"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen
                                            ></iframe>
                                        </div>
                                        
                                        <!-- Vimeo player -->
                                        <div v-else-if="isVimeoUrl" class="aspect-video">
                                            <iframe 
                                                :src="vimeoEmbedUrl"
                                                class="w-full h-full"
                                                frameborder="0"
                                                allow="autoplay; fullscreen; picture-in-picture"
                                                allowfullscreen
                                            ></iframe>
                                        </div>
                                        
                                        <!-- HTML5 video for other URLs -->
                                        <video 
                                            v-else
                                            controls
                                            class="w-full aspect-video"
                                            :poster="video?.thumbnail_url"
                                        >
                                            <source :src="video?.video_url" type="video/mp4">
                                            {{ $t('video_preview.video_unavailable') }}
                                        </video>
                                    </div>
                                </div>

                                <!-- Video information -->
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <div class="lg:col-span-2">
                                        <h3 class="text-xl font-semibold mb-2">{{ video?.title }}</h3>
                                        <p class="text-gray-600 dark:text-gray-300 mb-4" v-if="video?.description">
                                            {{ video?.description }}
                                        </p>
                                        
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <span class="badge bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $t('video_preview.duration') }}: {{ formatDuration(video?.duration_seconds || 0) }}
                                            </span>
                                            <span v-if="video?.is_free" class="badge bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ $t('video_preview.free') }}
                                            </span>
                                            <span v-else class="badge bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                {{ $t('video_preview.paid') }}
                                            </span>
                                            <span v-if="video?.is_active" class="badge bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ $t('video_preview.active') }}
                                            </span>
                                            <span v-else class="badge bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                {{ $t('video_preview.inactive') }}
                                            </span>
                                            <span class="badge bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                {{ $t('video_preview.order') }}: {{ video?.order || 0 }}
                                            </span>
                                        </div>

                                        <!-- Tags -->
                                        <div v-if="video?.tags && video.tags.length > 0" class="mb-4">
                                            <h4 class="font-medium mb-2">{{ $t('video_preview.tags') }}:</h4>
                                            <div class="flex flex-wrap gap-1">
                                                <span 
                                                    v-for="tag in video.tags" 
                                                    :key="tag"
                                                    class="badge bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200"
                                                >
                                                    {{ tag }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lg:col-span-1">
                                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                            <h4 class="font-semibold mb-3">{{ $t('video_preview.technical_info') }}</h4>
                                            
                                            <div class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">{{ $t('video_preview.slug') }}:</span>
                                                    <span class="font-mono text-xs">{{ video?.slug }}</span>
                                                </div>
                                                
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">{{ $t('video_preview.created') }}:</span>
                                                    <span>{{ formatDate(video?.created_at) }}</span>
                                                </div>
                                                
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">{{ $t('video_preview.updated') }}:</span>
                                                    <span>{{ formatDate(video?.updated_at) }}</span>
                                                </div>
                                                
                                                <div class="pt-2 border-t dark:border-gray-700">
                                                    <a 
                                                        :href="video?.video_url" 
                                                        target="_blank" 
                                                        class="btn btn-outline-primary btn-sm w-full"
                                                    >
                                                        <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                        {{ $t('video_preview.open_new_tab') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script lang="ts" setup>
    import { ref, computed } from 'vue';
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
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
        created_at?: string;
        updated_at?: string;
    }

    interface Props {
        show: boolean;
        video: Video | null;
    }

    interface Emits {
        (e: 'close'): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    // Computed properties para detectar el tipo de URL
    const isYouTubeUrl = computed(() => {
        return props.video?.video_url?.includes('youtube.com') || props.video?.video_url?.includes('youtu.be');
    });

    const isVimeoUrl = computed(() => {
        return props.video?.video_url?.includes('vimeo.com');
    });

    // Computed properties para URLs embebidas
    const youtubeEmbedUrl = computed(() => {
        if (!props.video?.video_url) return '';
        
        let videoId = '';
        const url = props.video.video_url;
        
        if (url.includes('youtube.com/watch?v=')) {
            videoId = url.split('v=')[1]?.split('&')[0] || '';
        } else if (url.includes('youtu.be/')) {
            videoId = url.split('youtu.be/')[1]?.split('?')[0] || '';
        }
        
        return `https://www.youtube.com/embed/${videoId}?autoplay=1`;
    });

    const vimeoEmbedUrl = computed(() => {
        if (!props.video?.video_url) return '';
        
        const videoId = props.video.video_url.split('vimeo.com/')[1]?.split('?')[0] || '';
        return `https://player.vimeo.com/video/${videoId}?autoplay=1`;
    });

    const formatDuration = (seconds: number): string => {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        
        if (hours > 0) {
            return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        } else {
            return `${minutes}:${secs.toString().padStart(2, '0')}`;
        }
    };

    const formatDate = (dateString?: string): string => {
        if (!dateString) return t('video_preview.not_available');
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const closeModal = () => {
        emit('close');
    };
</script>

<style scoped>
.aspect-video {
    aspect-ratio: 16 / 9;
}
</style>