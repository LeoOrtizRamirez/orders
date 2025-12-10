<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="$emit('close')" class="relative z-[51]">
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
                                @click="$emit('close')"
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
                                {{ $t(params.id ? 'products_page.modal.edit_title' : 'products_page.modal.add_title') }}
                            </div>
                            <div class="p-5">
                                <form @submit.prevent="handleSubmit">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="mb-5">
                                            <label for="name">{{ $t('products_page.modal.fields.name') }} *</label>
                                            <input 
                                                id="name" 
                                                type="text" 
                                                :placeholder="$t('products_page.modal.placeholders.name')" 
                                                class="form-input" 
                                                v-model="params.name" 
                                                required 
                                            />
                                            <div v-if="errors.name" class="text-danger mt-1">{{ errors.name[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="sku">{{ $t('products_page.modal.fields.sku') }}</label>
                                            <input 
                                                id="sku" 
                                                type="text" 
                                                :placeholder="$t('products_page.modal.placeholders.sku')" 
                                                class="form-input" 
                                                v-model="params.sku" 
                                            />
                                            <div v-if="errors.sku" class="text-danger mt-1">{{ errors.sku[0] }}</div>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <label for="description">{{ $t('products_page.modal.fields.description') }}</label>
                                        <textarea 
                                            id="description" 
                                            :placeholder="$t('products_page.modal.placeholders.description')" 
                                            class="form-textarea min-h-[80px]" 
                                            v-model="params.description"
                                        ></textarea>
                                        <div v-if="errors.description" class="text-danger mt-1">{{ errors.description[0] }}</div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="mb-5">
                                            <label for="stock">{{ $t('products_page.modal.fields.stock') }}</label>
                                            <input 
                                                id="stock" 
                                                type="number" 
                                                min="0" 
                                                :placeholder="$t('products_page.modal.placeholders.stock')" 
                                                class="form-input" 
                                                v-model="params.stock" 
                                            />
                                            <div v-if="errors.stock" class="text-danger mt-1">{{ errors.stock[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="min_stock">{{ $t('products_page.modal.fields.min_stock') }}</label>
                                            <input 
                                                id="min_stock" 
                                                type="number" 
                                                min="0" 
                                                :placeholder="$t('products_page.modal.placeholders.min_stock')" 
                                                class="form-input" 
                                                v-model="params.min_stock" 
                                            />
                                            <div v-if="errors.min_stock" class="text-danger mt-1">{{ errors.min_stock[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="reorder_point">{{ $t('products_page.modal.fields.reorder_point') }}</label>
                                            <input 
                                                id="reorder_point" 
                                                type="number" 
                                                min="0" 
                                                :placeholder="$t('products_page.modal.placeholders.reorder_point')" 
                                                class="form-input" 
                                                v-model="params.reorder_point" 
                                            />
                                            <div v-if="errors.reorder_point" class="text-danger mt-1">{{ errors.reorder_point[0] }}</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="mb-5">
                                            <label for="unit">{{ $t('products_page.modal.fields.unit') }}</label>
                                            <input 
                                                id="unit" 
                                                type="text" 
                                                :placeholder="$t('products_page.modal.placeholders.unit')" 
                                                class="form-input" 
                                                v-model="params.unit" 
                                            />
                                            <div v-if="errors.unit" class="text-danger mt-1">{{ errors.unit[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="category">{{ $t('products_page.modal.fields.category') }}</label>
                                            <input 
                                                id="category" 
                                                type="text" 
                                                :placeholder="$t('products_page.modal.placeholders.category')" 
                                                class="form-input" 
                                                v-model="params.category" 
                                                list="categories" 
                                            />
                                            <datalist id="categories">
                                                <option v-for="category in categories" :key="category" :value="category" />
                                            </datalist>
                                            <div v-if="errors.category" class="text-danger mt-1">{{ errors.category[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="order">{{ $t('products_page.modal.fields.order') }}</label>
                                            <input 
                                                id="order" 
                                                type="number" 
                                                min="0" 
                                                :placeholder="$t('products_page.modal.placeholders.order')" 
                                                class="form-input" 
                                                v-model="params.order" 
                                            />
                                            <div v-if="errors.order" class="text-danger mt-1">{{ errors.order[0] }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!--
                                        <div class="mb-5">
                                            <label for="brand">{{ $t('products_page.modal.fields.brand') }}</label>
                                            <input 
                                                id="brand" 
                                                type="text" 
                                                :placeholder="$t('products_page.modal.placeholders.brand')" 
                                                class="form-input" 
                                                v-model="params.brand" 
                                            />
                                            <div v-if="errors.brand" class="text-danger mt-1">{{ errors.brand[0] }}</div>
                                        </div>
                                        -->
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="mb-5">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="checkbox" class="form-checkbox" v-model="params.is_active" />
                                                <span class="text-white-dark ltr:ml-3 rtl:mr-3">{{ $t('products_page.modal.fields.active') }}</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex justify-end items-center mt-8">
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-danger" 
                                            @click="$emit('close')"
                                            :disabled="saving"
                                        >
                                            {{ $t('products_page.modal.buttons.cancel') }}
                                        </button>
                                        <button 
                                            type="submit" 
                                            class="btn btn-primary ltr:ml-4 rtl:mr-4" 
                                            :disabled="saving"
                                        >
                                            <span 
                                                v-if="saving" 
                                                class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4 ltr:mr-2 rtl:ml-2 inline-block"
                                            ></span>
                                            {{ params.id 
                                                ? $t('products_page.modal.buttons.update') 
                                                : $t('products_page.modal.buttons.add') 
                                            }}
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
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';

    interface Props {
        show: boolean;
        params: any;
        errors: Record<string, string[]>;
        saving: boolean;
        categories: string[];
    }

    interface Emits {
        (e: 'close'): void;
        (e: 'save', params: any): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    const handleSubmit = async () => {
        emit('save', props.params);
    };
</script>