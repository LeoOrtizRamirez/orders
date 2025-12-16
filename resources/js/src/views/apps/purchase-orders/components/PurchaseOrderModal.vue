<template>
    <div class="fixed inset-0 bg-[black]/60 z-50 overflow-y-auto" v-if="show">
        <div class="flex items-start justify-center min-h-screen px-4">
            <div class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-6xl my-8">
                <!-- Header -->
                <div class="flex bg-[#fbfbfb] dark:bg-[#1c232f] items-center justify-between px-5 py-3">
                    <h5 class="font-bold text-lg">
                        {{ params.id ? $t('purchase_orders_page.create_modal.edit_title') : $t('purchase_orders_page.create_modal.add_title') }}
                    </h5>
                    <button type="button" class="text-white-dark hover:text-dark" @click="handleClose()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-5">
                    <form @submit.prevent="$emit('save', params)">
                        <!-- Información Básica -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                            <!-- Proveedor -->
                            <div>
                                <label for="supplier" class="form-label">
                                    {{ $t('purchase_orders_page.create_modal.fields.supplier') }} *
                                </label>
                                <select
                                    id="supplier"
                                    v-model="params.supplier_id"
                                    class="form-select"
                                    :class="{ 'border-danger': errors.supplier_id }"
                                    required
                                >
                                    <option value="">{{ $t('purchase_orders_page.create_modal.placeholders.select_supplier') }}</option>
                                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                        {{ supplier.name }}
                                    </option>
                                </select>
                                <div v-if="errors.supplier_id" class="text-danger mt-1 text-xs">
                                    {{ errors.supplier_id[0] }}
                                </div>
                            </div>

                            <!-- Fecha de entrega esperada -->
                            <div>
                                <label for="expected_delivery_date" class="form-label">
                                    {{ $t('purchase_orders_page.create_modal.fields.expected_delivery_date') }} *
                                </label>
                                <input
                                    id="expected_delivery_date"
                                    type="date"
                                    v-model="params.expected_delivery_date"
                                    class="form-input"
                                    :class="{ 'border-danger': errors.expected_delivery_date }"
                                    required
                                />
                                <div v-if="errors.expected_delivery_date" class="text-danger mt-1 text-xs">
                                    {{ errors.expected_delivery_date[0] }}
                                </div>
                            </div>
                        </div>

                        <!-- Items de la orden -->
                        <div class="mb-5">
                            <div class="flex justify-between items-center mb-3">
                                <label class="form-label">
                                    {{ $t('purchase_orders_page.create_modal.fields.items') }} *
                                </label>
                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm"
                                    @click="$emit('add-item')"
                                >
                                    + {{ $t('purchase_orders_page.create_modal.buttons.add_item') }}
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-for="(item, index) in params.items"
                                    :key="index"
                                    class="border border-[#e0e6ed] dark:border-[#1b2e4b] rounded p-4 bg-gray-50 dark:bg-[#1a2234]"
                                >
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                        <!-- Producto -->
                                        <div class="md:col-span-6">
                                            <label class="form-label">
                                                {{ $t('purchase_orders_page.create_modal.fields.product') }} *
                                            </label>
                                            <select
                                                v-model="item.product_id"
                                                class="form-select"
                                                :class="{ 
                                                    'border-danger': errors[`items.${index}.product_id`] || 
                                                    !item.product_id && item.quantity > 0 
                                                }"
                                                required
                                            >
                                                <option value="">{{ $t('purchase_orders_page.create_modal.placeholders.select_product') }}</option>
                                                <option
                                                    v-for="product in availableProducts"
                                                    :key="product.id"
                                                    :value="product.id"
                                                    :disabled="!product.stock || product.stock <= 0"
                                                >
                                                    {{ product.name }} 
                                                    <span v-if="!product.stock || product.stock <= 0" class="text-danger text-xs">
                                                        (Sin stock)
                                                    </span>
                                                    <span v-else class="text-success text-xs">
                                                        (Stock: {{ product.stock }})
                                                    </span>
                                                </option>
                                            </select>
                                            <div v-if="errors[`items.${index}.product_id`]" class="text-danger mt-1 text-xs">
                                                {{ errors[`items.${index}.product_id`][0] }}
                                            </div>
                                        </div>

                                        <!-- Cantidad -->
                                        <div class="md:col-span-4">
                                            <label class="form-label">
                                                {{ $t('purchase_orders_page.create_modal.fields.quantity') }} *
                                            </label>
                                            <input
                                                type="number"
                                                v-model.number="item.quantity"
                                                min="1"
                                                class="form-input"
                                                :class="{ 
                                                    'border-danger': errors[`items.${index}.quantity`] 
                                                }"
                                                @input="handleQuantityChange(index, item.quantity)"
                                                required
                                            />
                                            <div v-if="errors[`items.${index}.quantity`]" class="text-danger mt-1 text-xs">
                                                {{ errors[`items.${index}.quantity`][0] }}
                                            </div>
                                            <div v-if="item.product_id && getMaxQuantity(item.product_id) > 0" 
                                                 class="text-info mt-1 text-xs">
                                                Stock actual: {{ getMaxQuantity(item.product_id) }}
                                            </div>

                                            <!-- Notas del item -->
                                            <div class="mt-3">
                                                <label class="form-label font-semibold text-xs">{{ $t('user_notes.notes_label') }}</label>
                                                <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded space-y-2 max-h-24 overflow-y-auto mb-2">
                                                    <div v-for="note in item.itemNotes" :key="note.id" class="text-xs pb-1 border-b dark:border-gray-600 last:border-b-0">
                                                        <p class="font-semibold">{{ note.author?.name }} <span class="text-gray-500 text-xs">- {{ formatNoteTimestamp(note.created_at) }}</span></p>
                                                        <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ note.note }}</p>
                                                    </div>
                                                    <div v-if="!item.itemNotes || item.itemNotes.length === 0" class="text-gray-500 text-xs">
                                                        {{ $t('user_notes.no_notes_for_item') }}
                                                    </div>
                                                </div>
                                                <input type="text" class="form-input text-xs" :placeholder="$t('purchase_orders_page.create_modal.placeholders.item_notes')" v-model="item.notes">
                                            </div>
                                        </div>
                                        
                                        <!-- Botón Eliminar Item -->
                                        <div class="md:col-span-2 flex justify-end mt-7">
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                @click="$emit('remove-item', index)"
                                                v-if="params.items.length > 1"
                                            >
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                                    <path d="M14.5 9L9.5 14M9.5 9L14.5 14M7.2 20H16.8C17.9201 20 18.4802 20 18.908 19.782C19.2843 19.5903 19.5903 19.2843 19.782 18.908C20 18.4802 20 17.9201 20 16.8V7.2C20 6.0799 20 5.51984 19.782 5.09202C19.5903 4.71569 19.2843 4.40973 18.908 4.21799C18.4802 4 17.9201 4 16.8 4H7.2C6.0799 4 5.51984 4 5.09202 4.21799C4.71569 4.40973 4.40973 4.71569 4.21799 5.09202C4 5.51984 4 6.0799 4 7.2V16.8C4 17.9201 4 18.4802 4.21799 18.908C4.40973 19.2843 4.71569 19.5903 5.09202 19.782C5.51984 20 6.0799 20 7.2 20Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notas de la Orden -->
                        <div class="mb-5">
                            <label class="form-label font-semibold">{{ $t('purchase_orders_page.view_modal.fields.notes') }}</label>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded space-y-3 mb-3">
                                <div v-for="note in existingOrderNotes" :key="note.id" class="text-sm pb-2 border-b dark:border-gray-700 last:border-b-0">
                                    <p class="font-semibold">{{ note.author?.name }} <span class="text-gray-500 text-xs">- {{ formatNoteTimestamp(note.created_at) }}</span></p>
                                    <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ note.note }}</p>
                                </div>
                                <div v-if="!existingOrderNotes || existingOrderNotes.length === 0" class="text-gray-500">
                                    {{ $t('user_notes.no_notes_general') }}
                                </div>
                            </div>
                            
                            <label for="notes" class="form-label text-xs">
                                {{ $t('purchase_orders_page.create_modal.fields.add_note') }}
                            </label>
                            <textarea
                                id="notes"
                                v-model="params.notes"
                                class="form-textarea min-h-[80px]"
                                :placeholder="$t('purchase_orders_page.create_modal.placeholders.notes')"
                                rows="3"
                            ></textarea>
                        </div>
                        


                        <!-- Botones -->
                        <div class="flex justify-end items-center mt-8 border-t pt-4">
                            <button
                                type="button"
                                class="btn btn-outline-danger mr-3"
                                @click="handleClose()"
                                :disabled="saving"
                            >
                                {{ $t('purchase_orders_page.create_modal.buttons.cancel') }}
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="saving || !isFormValid"
                            >
                                <span v-if="saving" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ $t('purchase_orders_page.create_modal.buttons.saving') }}
                                </span>
                                <span v-else>
                                    {{ params.id 
                                        ? $t('purchase_orders_page.create_modal.buttons.update') 
                                        : $t('purchase_orders_page.create_modal.buttons.add') 
                                    }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import { computed, ref } from 'vue';
    import type { UserNote, PurchaseOrderParams } from '@/types/purchase-orders';
    import { useI18n } from 'vue-i18n';

    const { t } = useI18n();

    interface Props {
        show: boolean;
        params: PurchaseOrderParams; // Use the more specific type
        errors: any;
        saving: boolean;
        suppliers: any[];
        products: any[];
        existingOrderNotes: any[]; // Changed from orderNotes to existingOrderNotes
    }

    interface Emits {
        (e: 'close'): void;
        (e: 'save', params: PurchaseOrderParams): void;
        (e: 'add-item'): void;
        (e: 'remove-item', index: number): void;
        (e: 'refresh-view-order'): void; // Emit to refresh notes after adding/deleting
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    // Computed
    const availableProducts = computed(() => {
        return props.products.filter(product => product.is_active !== false);
    });

    const isFormValid = computed(() => {
        const hasValidItems = props.params.items.length > 0 && 
            props.params.items.every((item: any) => 
                item.product_id && 
                item.quantity > 0
            );
        
        return props.params.supplier_id && 
               props.params.expected_delivery_date &&
               hasValidItems;
    });

    // Methods
    const handleQuantityChange = (index: number, quantity: number) => {
        // Validar cantidad en tiempo real
        if (quantity < 1) {
            props.params.items[index].quantity = 1;
        }
    };

    const getMaxQuantity = (productId: number | null): number => {
        if (!productId) return 0;
        const product = props.products.find(p => p.id === productId);
        return product?.stock || 0;
    };

    const getQuantityValidation = (productId: number | null, quantity: number) => {
        if (!productId) return { hasError: false, message: '' };
        return { hasError: false, message: '' };
    };

    const formatNoteTimestamp = (dateString: string | null): string => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const handleClose = () => {
        console.log('PurchaseOrderModal: Emitting close event');
        emit('close');
    };
</script>