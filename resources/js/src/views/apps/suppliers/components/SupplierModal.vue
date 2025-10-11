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
                        <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-3xl text-black dark:text-white-dark">
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
                                {{ $t(params.id ? 'suppliers_page.modal.edit_title' : 'suppliers_page.modal.add_title') }}
                            </div>
                            <div class="p-5">
                                <form @submit.prevent="handleSubmit">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="mb-5">
                                            <label for="name">{{ $t('suppliers_page.modal.fields.name') }} *</label>
                                            <input 
                                                id="name" 
                                                type="text" 
                                                :placeholder="$t('suppliers_page.modal.placeholders.name')" 
                                                class="form-input" 
                                                v-model="params.name" 
                                                required 
                                            />
                                            <div v-if="errors.name" class="text-danger mt-1">{{ errors.name[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="contact_person">{{ $t('suppliers_page.modal.fields.contact_person') }}</label>
                                            <input 
                                                id="contact_person" 
                                                type="text" 
                                                :placeholder="$t('suppliers_page.modal.placeholders.contact_person')" 
                                                class="form-input" 
                                                v-model="params.contact_person" 
                                            />
                                            <div v-if="errors.contact_person" class="text-danger mt-1">{{ errors.contact_person[0] }}</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="mb-5">
                                            <label for="email">{{ $t('suppliers_page.modal.fields.email') }}</label>
                                            <input 
                                                id="email" 
                                                type="email" 
                                                :placeholder="$t('suppliers_page.modal.placeholders.email')" 
                                                class="form-input" 
                                                v-model="params.email" 
                                            />
                                            <div v-if="errors.email" class="text-danger mt-1">{{ errors.email[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="phone">{{ $t('suppliers_page.modal.fields.phone') }}</label>
                                            <input 
                                                id="phone" 
                                                type="text" 
                                                :placeholder="$t('suppliers_page.modal.placeholders.phone')" 
                                                class="form-input" 
                                                v-model="params.phone" 
                                            />
                                            <div v-if="errors.phone" class="text-danger mt-1">{{ errors.phone[0] }}</div>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <label for="address">{{ $t('suppliers_page.modal.fields.address') }}</label>
                                        <textarea 
                                            id="address" 
                                            :placeholder="$t('suppliers_page.modal.placeholders.address')" 
                                            class="form-textarea min-h-[80px]" 
                                            v-model="params.address"
                                        ></textarea>
                                        <div v-if="errors.address" class="text-danger mt-1">{{ errors.address[0] }}</div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="mb-5">
                                            <label for="city">{{ $t('suppliers_page.modal.fields.city') }}</label>
                                            <input 
                                                id="city" 
                                                type="text" 
                                                :placeholder="$t('suppliers_page.modal.placeholders.city')" 
                                                class="form-input" 
                                                v-model="params.city" 
                                            />
                                            <div v-if="errors.city" class="text-danger mt-1">{{ errors.city[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="country">{{ $t('suppliers_page.modal.fields.country') }}</label>
                                            <input 
                                                id="country" 
                                                type="text" 
                                                :placeholder="$t('suppliers_page.modal.placeholders.country')" 
                                                class="form-input" 
                                                v-model="params.country" 
                                            />
                                            <div v-if="errors.country" class="text-danger mt-1">{{ errors.country[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="tax_id">{{ $t('suppliers_page.modal.fields.tax_id') }}</label>
                                            <input 
                                                id="tax_id" 
                                                type="text" 
                                                :placeholder="$t('suppliers_page.modal.placeholders.tax_id')" 
                                                class="form-input" 
                                                v-model="params.tax_id" 
                                            />
                                            <div v-if="errors.tax_id" class="text-danger mt-1">{{ errors.tax_id[0] }}</div>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <label for="payment_terms">{{ $t('suppliers_page.modal.fields.payment_terms') }}</label>
                                        <textarea 
                                            id="payment_terms" 
                                            :placeholder="$t('suppliers_page.modal.placeholders.payment_terms')" 
                                            class="form-textarea min-h-[60px]" 
                                            v-model="params.payment_terms"
                                        ></textarea>
                                        <div v-if="errors.payment_terms" class="text-danger mt-1">{{ errors.payment_terms[0] }}</div>
                                    </div>

                                    <div class="mb-5">
                                        <label for="notes">{{ $t('suppliers_page.modal.fields.notes') }}</label>
                                        <textarea 
                                            id="notes" 
                                            :placeholder="$t('suppliers_page.modal.placeholders.notes')" 
                                            class="form-textarea min-h-[80px]" 
                                            v-model="params.notes"
                                        ></textarea>
                                        <div v-if="errors.notes" class="text-danger mt-1">{{ errors.notes[0] }}</div>
                                    </div>

                                    <div class="mb-5">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" class="form-checkbox" v-model="params.is_active" />
                                            <span class="text-white-dark ltr:ml-3 rtl:mr-3">{{ $t('suppliers_page.modal.fields.active') }}</span>
                                        </label>
                                    </div>

                                    <div class="flex justify-end items-center mt-8">
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-danger" 
                                            @click="$emit('close')"
                                            :disabled="saving"
                                        >
                                            {{ $t('suppliers_page.modal.buttons.cancel') }}
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
                                                ? $t('suppliers_page.modal.buttons.update') 
                                                : $t('suppliers_page.modal.buttons.add') 
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