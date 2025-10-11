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
                        <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-4xl text-black dark:text-white-dark">
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
                                {{ $t(params.id ? 'purchase_orders_page.modal.edit_title' : 'purchase_orders_page.modal.add_title') }}
                            </div>
                            <div class="p-5">
                                <form @submit.prevent="handleSubmit">
                                    <!-- Información Básica -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                        <div class="mb-5">
                                            <label for="supplier_id">{{ $t('purchase_orders_page.modal.fields.supplier') }} *</label>
                                            <select 
                                                id="supplier_id" 
                                                class="form-select" 
                                                v-model="params.supplier_id"
                                                required
                                            >
                                                <option value="">{{ $t('purchase_orders_page.modal.placeholders.select_supplier') }}</option>
                                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                                    {{ supplier.name }}
                                                </option>
                                            </select>
                                            <div v-if="errors.supplier_id" class="text-danger mt-1">{{ errors.supplier_id[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="expected_delivery_date">{{ $t('purchase_orders_page.modal.fields.expected_delivery_date') }}</label>
                                            <input 
                                                id="expected_delivery_date" 
                                                type="date" 
                                                class="form-input" 
                                                v-model="params.expected_delivery_date"
                                            />
                                            <div v-if="errors.expected_delivery_date" class="text-danger mt-1">{{ errors.expected_delivery_date[0] }}</div>
                                        </div>
                                    </div>

                                    <!-- Items de la Orden -->
                                    <div class="mb-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <label class="text-lg font-semibold">{{ $t('purchase_orders_page.modal.fields.items') }}</label>
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-primary btn-sm"
                                                @click="$emit('add-item')"
                                            >
                                                <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                {{ $t('purchase_orders_page.modal.buttons.add_item') }}
                                            </button>
                                        </div>

                                        <div class="space-y-4">
                                            <div 
                                                v-for="(item, index) in params.items" 
                                                :key="index"
                                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4"
                                            >
                                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                                    <!-- Producto -->
                                                    <div class="md:col-span-4">
                                                        <label class="text-sm">{{ $t('purchase_orders_page.modal.fields.product') }} *</label>
                                                        <select 
                                                            class="form-select" 
                                                            v-model="item.product_id"
                                                            required
                                                        >
                                                            <option value="">{{ $t('purchase_orders_page.modal.placeholders.select_product') }}</option>
                                                            <option v-for="product in products" :key="product.id" :value="product.id">
                                                                {{ product.name }} - {{ moneyFormat(product.price) }}
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <!-- Cantidad -->
                                                    <div class="md:col-span-2">
                                                        <label class="text-sm">{{ $t('purchase_orders_page.modal.fields.quantity') }} *</label>
                                                        <input 
                                                            type="number" 
                                                            min="1" 
                                                            class="form-input" 
                                                            v-model="item.quantity"
                                                            @input="updateItemTotal(item)"
                                                            required
                                                        />
                                                    </div>

                                                    <!-- Precio Unitario -->
                                                    <div class="md:col-span-2">
                                                        <label class="text-sm">{{ $t('purchase_orders_page.modal.fields.unit_price') }} *</label>
                                                        <input 
                                                            type="number" 
                                                            step="0.01" 
                                                            min="0" 
                                                            class="form-input" 
                                                            v-model="item.unit_price"
                                                            @input="updateItemTotal(item)"
                                                            required
                                                        />
                                                    </div>

                                                    <!-- Total -->
                                                    <div class="md:col-span-2">
                                                        <label class="text-sm">{{ $t('purchase_orders_page.modal.fields.total_price') }}</label>
                                                        <div class="form-input bg-gray-50 dark:bg-gray-800">
                                                            {{ moneyFormat(calculateItemTotal(item)) }}
                                                        </div>
                                                    </div>

                                                    <!-- Eliminar -->
                                                    <div class="md:col-span-2">
                                                        <button 
                                                            type="button" 
                                                            class="btn btn-outline-danger w-full"
                                                            @click="$emit('remove-item', index)"
                                                            :disabled="params.items.length === 1"
                                                        >
                                                            {{ $t('purchase_orders_page.modal.buttons.remove_item') }}
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Notas del Item -->
                                                <div class="mt-3">
                                                    <label class="text-sm">{{ $t('purchase_orders_page.modal.fields.item_notes') }}</label>
                                                    <input 
                                                        type="text" 
                                                        class="form-input" 
                                                        :placeholder="$t('purchase_orders_page.modal.placeholders.item_notes')"
                                                        v-model="item.notes"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Totales y Costos Adicionales -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div class="space-y-4">
                                            <div class="mb-5">
                                                <label for="tax">{{ $t('purchase_orders_page.modal.fields.tax') }}</label>
                                                <input 
                                                    id="tax" 
                                                    type="number" 
                                                    step="0.01" 
                                                    min="0" 
                                                    :placeholder="$t('purchase_orders_page.modal.placeholders.tax')" 
                                                    class="form-input" 
                                                    v-model="params.tax"
                                                />
                                                <div v-if="errors.tax" class="text-danger mt-1">{{ errors.tax[0] }}</div>
                                            </div>
                                            <div class="mb-5">
                                                <label for="shipping">{{ $t('purchase_orders_page.modal.fields.shipping') }}</label>
                                                <input 
                                                    id="shipping" 
                                                    type="number" 
                                                    step="0.01" 
                                                    min="0" 
                                                    :placeholder="$t('purchase_orders_page.modal.placeholders.shipping')" 
                                                    class="form-input" 
                                                    v-model="params.shipping"
                                                />
                                                <div v-if="errors.shipping" class="text-danger mt-1">{{ errors.shipping[0] }}</div>
                                            </div>
                                        </div>

                                        <!-- Resumen de Totales -->
                                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                            <h4 class="font-semibold mb-3">Resumen de Totales</h4>
                                            <div class="space-y-2">
                                                <div class="flex justify-between">
                                                    <span>Subtotal:</span>
                                                    <span class="font-semibold">{{ moneyFormat(calculateSubtotal()) }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Impuestos:</span>
                                                    <span class="font-semibold">{{ moneyFormat(params.tax || 0) }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Envío:</span>
                                                    <span class="font-semibold">{{ moneyFormat(params.shipping || 0) }}</span>
                                                </div>
                                                <hr class="my-2">
                                                <div class="flex justify-between text-lg font-bold">
                                                    <span>Total:</span>
                                                    <span class="text-success">{{ moneyFormat(calculateTotal()) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notas -->
                                    <div class="mb-6">
                                        <label for="notes">{{ $t('purchase_orders_page.modal.fields.notes') }}</label>
                                        <textarea 
                                            id="notes" 
                                            :placeholder="$t('purchase_orders_page.modal.placeholders.notes')" 
                                            class="form-textarea min-h-[80px]" 
                                            v-model="params.notes"
                                        ></textarea>
                                        <div v-if="errors.notes" class="text-danger mt-1">{{ errors.notes[0] }}</div>
                                    </div>

                                    <!-- Botones -->
                                    <div class="flex justify-end items-center mt-8">
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-danger" 
                                            @click="$emit('close')"
                                            :disabled="saving"
                                        >
                                            {{ $t('purchase_orders_page.modal.buttons.cancel') }}
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
                                                ? $t('purchase_orders_page.modal.buttons.update') 
                                                : $t('purchase_orders_page.modal.buttons.add') 
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
    import type { PurchaseOrderParams, Supplier, Product } from '@/types/purchase-orders';

    interface Props {
        show: boolean;
        params: PurchaseOrderParams;
        errors: Record<string, string[]>;
        saving: boolean;
        suppliers: Supplier[];
        products: Product[];
    }

    interface Emits {
        (e: 'close'): void;
        (e: 'save', params: PurchaseOrderParams): void;
        (e: 'add-item'): void;
        (e: 'remove-item', index: number): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    const handleSubmit = async () => {
        emit('save', props.params);
    };

    const calculateItemTotal = (item: any): number => {
        return (item.quantity || 0) * (item.unit_price || 0);
    };

    const calculateSubtotal = (): number => {
        return props.params.items.reduce((total, item) => total + calculateItemTotal(item), 0);
    };

    const calculateTotal = (): number => {
        return calculateSubtotal() + (props.params.tax || 0) + (props.params.shipping || 0);
    };

    const updateItemTotal = (item: any) => {
        // Esta función se llama automáticamente cuando cambia quantity o unit_price
        // El total se calcula automáticamente en calculateItemTotal
    };

    const moneyFormat = (valor = 0): string => {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(valor);
    };
</script>