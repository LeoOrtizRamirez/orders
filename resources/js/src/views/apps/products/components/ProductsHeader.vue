<template>
    <div class="flex items-center justify-between flex-wrap gap-4">
        <h2 class="text-xl">{{ $t('products_page.title') }}</h2>
        <div class="flex sm:flex-row flex-col sm:items-center sm:gap-3 gap-4 w-full sm:w-auto">
            <div class="flex gap-3">
                <!-- Botones de Acción -->
                <div class="flex gap-3">
                    <button 
                        v-if="authStore.can('create products')"
                        type="button" 
                        class="btn btn-primary" 
                        @click="$emit('add-product')"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
                            <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" stroke="currentColor" stroke-width="1.5"/>
                            <path opacity="0.5" d="M8 12H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path opacity="0.5" d="M12 16V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        {{ $t('products_page.add_product_btn') }}
                    </button>

                    <button 
                        v-if="authStore.can('create products')"
                        type="button" 
                        class="btn btn-outline-primary" 
                        @click="$emit('open-import-modal')"
                    >
                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        {{ $t('products_page.import_modal.upload_btn') }}
                    </button>

                    <button 
                        v-if="authStore.can('create products')"
                        type="button" 
                        class="btn btn-outline-primary" 
                        @click="$emit('download-template')"
                    >
                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        {{ $t('products_page.import_modal.download_template_btn') }}
                    </button>
                </div>

                <!-- Botones de vista -->
                <div>
                    <button
                        type="button"
                        class="btn btn-outline-primary p-2"
                        :class="{ 'bg-primary text-white': displayType === 'list' }"
                        @click="$emit('update:display-type', 'list')"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                            <path d="M2 5.5L3.21429 7L7.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                opacity="0.5"
                                d="M2 12.5L3.21429 14L7.5 10"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path d="M2 19.5L3.21429 21L7.5 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M22 19L12 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path opacity="0.5" d="M22 12L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M22 5L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div>
                    <button
                        type="button"
                        class="btn btn-outline-primary p-2"
                        :class="{ 'bg-primary text-white': displayType === 'grid' }"
                        @click="$emit('update:display-type', 'grid')"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                            <path
                                opacity="0.5"
                                d="M2.5 6.5C2.5 4.61438 2.5 3.67157 3.08579 3.08579C3.67157 2.5 4.61438 2.5 6.5 2.5C8.38562 2.5 9.32843 2.5 9.91421 3.08579C10.5 3.67157 10.5 4.61438 10.5 6.5C10.5 8.38562 10.5 9.32843 9.91421 9.91421C9.32843 10.5 8.38562 10.5 6.5 10.5C4.61438 10.5 3.67157 10.5 3.08579 9.91421C2.5 9.32843 2.5 8.38562 2.5 6.5Z"
                                stroke="currentColor"
                                stroke-width="1.5"
                            />
                            <path
                                opacity="0.5"
                                d="M13.5 17.5C13.5 15.6144 13.5 14.6716 14.0858 14.0858C14.6716 13.5 15.6144 13.5 17.5 13.5C19.3856 13.5 20.3284 13.5 20.9142 14.0858C21.5 14.6716 21.5 15.6144 21.5 17.5C21.5 19.3856 21.5 20.3284 20.9142 20.9142C20.3284 21.5 19.3856 21.5 17.5 21.5C15.6144 21.5 14.6716 21.5 14.0858 20.9142C13.5 20.3284 13.5 19.3856 13.5 17.5Z"
                                stroke="currentColor"
                                stroke-width="1.5"
                            />
                            <path
                                d="M2.5 17.5C2.5 15.6144 2.5 14.6716 3.08579 14.0858C3.67157 13.5 4.61438 13.5 6.5 13.5C8.38562 13.5 9.32843 13.5 9.91421 14.0858C10.5 14.6716 10.5 15.6144 10.5 17.5C10.5 19.3856 10.5 20.3284 9.91421 20.9142C9.32843 21.5 8.38562 21.5 6.5 21.5C4.61438 21.5 3.67157 21.5 3.08579 20.9142C2.5 20.3284 2.5 19.3856 2.5 17.5Z"
                                stroke="currentColor"
                                stroke-width="1.5"
                            />
                            <path
                                d="M13.5 6.5C13.5 4.61438 13.5 3.67157 14.0858 3.08579C14.6716 2.5 15.6144 2.5 17.5 2.5C19.3856 2.5 20.3284 2.5 20.9142 3.08579C21.5 3.67157 21.5 4.61438 21.5 6.5C21.5 8.38562 21.5 9.32843 20.9142 9.91421C20.3284 10.5 19.3856 10.5 17.5 10.5C15.6144 10.5 14.6716 10.5 14.0858 9.91421C13.5 9.32843 13.5 8.38562 13.5 6.5Z"
                                stroke="currentColor"
                                stroke-width="1.5"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="flex gap-2">
                <select 
                    v-model="localFilters.category" 
                    class="form-select py-2" 
                    @change="handleFiltersChange"
                >
                    <option value="">{{ $t('products_page.filters.all_categories') }}</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
                
                <select 
                    v-model="localFilters.stock_status" 
                    class="form-select py-2" 
                    @change="handleFiltersChange"
                >
                    <option value="">{{ $t('products_page.filters.all_stock') }}</option>
                    <option value="low">{{ $t('products_page.filters.low_stock') }}</option>
                    <option value="out">{{ $t('products_page.filters.out_of_stock') }}</option>
                </select>
            </div>

            <!-- Búsqueda -->
            <div class="relative">
                <input
                    type="text"
                    :placeholder="$t('products_page.search_placeholder')"
                    class="form-input py-2 ltr:pr-11 rtl:pl-11 peer"
                    :value="searchProduct"
                    @input="$emit('update:search-product', ($event.target as HTMLInputElement).value)"
                />
                <div class="absolute ltr:right-[11px] rtl:left-[11px] top-1/2 -translate-y-1/2 peer-focus:text-primary">
                    <svg class="mx-auto" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5" opacity="0.5"></circle>
                        <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import { ref, watch } from 'vue';
    import { useAuthStore } from '@/stores/auth';

    interface Props {
        displayType: 'list' | 'grid';
        filters: {
            category: string;
            stock_status: string;
        };
        categories: {id: string, name: string}[];
        searchProduct: string;
    }

    interface Emits {
        (e: 'update:display-type', value: 'list' | 'grid'): void;
        (e: 'update:filters', value: Props['filters']): void;
        (e: 'update:search-product', value: string): void;
        (e: 'add-product'): void;
        (e: 'open-import-modal'): void;
        (e: 'download-template'): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();
    const authStore = useAuthStore();

    const localFilters = ref({ ...props.filters });

    const handleFiltersChange = () => {
        emit('update:filters', { ...localFilters.value });
    };

    // Sincronizar cuando los props cambien
    watch(() => props.filters, (newFilters) => {
        localFilters.value = { ...newFilters };
    }, { deep: true });
</script>