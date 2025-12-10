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
                        <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-lg text-black dark:text-white-dark">
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
                                {{ $t('products_page.import_modal.title') }}
                            </div>
                            <div class="p-5">
                                <form @submit.prevent="handleImport">
                                    <div class="grid gap-5">
                                        <div class="mb-4">
                                            <label for="csvFile" class="block text-sm font-medium mb-1">
                                                {{ $t('products_page.import_modal.file_label') }}
                                            </label>
                                            <input
                                                id="csvFile"
                                                type="file"
                                                class="form-input"
                                                accept=".csv"
                                                @change="onFileChange"
                                            />
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $t('products_page.import_modal.supported_formats') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex justify-end items-center mt-8">
                                        <button type="button" class="btn btn-outline-danger" @click="$emit('close')">
                                            {{ $t('products_page.import_modal.cancel_btn') }}
                                        </button>
                                        <button
                                            type="submit"
                                            class="btn btn-primary ltr:ml-4 rtl:mr-4"
                                            :disabled="!selectedFile || loading"
                                        >
                                            <span v-if="loading" class="animate-spin border-2 border-white border-l-transparent rounded-full w-4 h-4 ltr:mr-2 rtl:ml-2 inline-block"></span>
                                            {{ loading ? $t('products_page.import_modal.loading_btn') : $t('products_page.import_modal.import_btn') }}
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
    import { ref } from 'vue';
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
    import { useI18n } from 'vue-i18n';

    const { t } = useI18n();

    interface Props {
        show: boolean;
        loading: boolean;
    }

    interface Emits {
        (e: 'close'): void;
        (e: 'import', file: File): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    const selectedFile = ref<File | null>(null);

    const onFileChange = (event: Event) => {
        const input = event.target as HTMLInputElement;
        if (input.files && input.files.length > 0) {
            selectedFile.value = input.files[0];
        } else {
            selectedFile.value = null;
        }
    };

    const handleImport = () => {
        if (selectedFile.value) {
            emit('import', selectedFile.value);
        }
    };
</script>