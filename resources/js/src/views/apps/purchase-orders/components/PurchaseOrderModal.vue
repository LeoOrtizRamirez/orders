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
                                    {{ $t('purchase_orders_page.create_modal.fields.supplier') }}
                                </label>
                                <select
                                    id="supplier"
                                    v-model="params.supplier_id"
                                    class="form-select"
                                    :class="{ 'border-danger': errors.supplier_id }"
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
                                    {{ $t('purchase_orders_page.create_modal.fields.expected_delivery_date') }}
                                </label>
                                <input
                                    id="expected_delivery_date"
                                    type="date"
                                    v-model="params.expected_delivery_date"
                                    class="form-input"
                                    :class="{ 'border-danger': errors.expected_delivery_date }"
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
                                    <div class="flex flex-col gap-3">
                                        <!-- Fila Principal: Checkbox - Producto - Cantidad - Eliminar -->
                                        <div class="flex items-start gap-3">
                                            <!-- Checkbox -->
                                            <div class="pt-2">
                                                <input type="checkbox" class="form-checkbox w-5 h-5" v-model="item.checked" />
                                            </div>

                                            <!-- Producto (Expandible) -->
                                            <div class="flex-grow">
                                                <multiselect
                                                    :model-value="getProductInfo(item.product_id)"
                                                    :options="availableProducts"
                                                    class="custom-multiselect"
                                                    :searchable="true"
                                                    track-by="id"
                                                    label="name"
                                                    :placeholder="$t('purchase_orders_page.create_modal.placeholders.select_product')"
                                                    selected-label=""
                                                    select-label=""
                                                    deselect-label=""
                                                    @update:model-value="(selected: any) => item.product_id = selected ? selected.id : null"
                                                >
                                                    <template #option="{ option }">
                                                        <div class="flex justify-between items-center w-full">
                                                            <span>{{ option.name }}</span>
                                                            <span v-if="!option.stock || option.stock <= 0" class="text-danger text-xs font-bold ml-2">
                                                                (Sin stock)
                                                            </span>
                                                            <span v-else class="text-success text-xs ml-2">
                                                                (Stock: {{ option.stock }})
                                                            </span>
                                                        </div>
                                                    </template>
                                                    <template #singleLabel="{ option }">
                                                        <div class="flex items-center">
                                                            <span>{{ option.name }}</span>
                                                            <span v-if="option.stock !== undefined" class="text-gray-500 text-xs ml-2">
                                                                (Stock: {{ option.stock }})
                                                            </span>
                                                        </div>
                                                    </template>
                                                </multiselect>
                                                <div v-if="errors[`items.${index}.product_id`]" class="text-danger mt-1 text-xs">
                                                    {{ errors[`items.${index}.product_id`][0] }}
                                                </div>
                                            </div>

                                            <!-- Cantidad (Fijo) -->
                                            <div class="w-24">
                                                <input
                                                    type="number"
                                                    v-model.number="item.quantity"
                                                    :min="0"
                                                    class="form-input h-[42px]" 
                                                    :class="{ 'border-danger': errors[`items.${index}.quantity`] }"
                                                    placeholder="Cant."
                                                    @input="handleQuantityChange(index, item.quantity)"
                                                    required
                                                />
                                            </div>

                                            <!-- Botón Eliminar -->
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-danger btn-sm h-[42px] px-3" 
                                                @click="$emit('remove-item', index)" 
                                                v-if="params.items.length > 1"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            </button>
                                        </div>

                                        <!-- Fila Secundaria: Errores de cantidad y Stock -->
                                        <div class="pl-8">
                                            <div v-if="errors[`items.${index}.quantity`]" class="text-danger text-xs mb-1">
                                                {{ errors[`items.${index}.quantity`][0] }}
                                            </div>
                                            <div v-if="item.product_id" class="text-xs" :class="getStockClass(item.product_id)">
                                                Stock actual: {{ getMaxQuantity(item.product_id) }}
                                            </div>
                                        </div>

                                        <!-- Fila Terciaria: Notas -->
                                        <div class="pl-8">
                                            <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded space-y-2 max-h-24 overflow-y-auto mb-2" v-if="item.itemNotes && item.itemNotes.length > 0">
                                                <div v-for="note in item.itemNotes" :key="note.id" class="text-xs pb-1 border-b dark:border-gray-600 last:border-b-0">
                                                    <p class="font-semibold">{{ note.author?.name }} <span class="text-gray-500 text-xs">- {{ formatNoteTimestamp(note.created_at) }}</span></p>
                                                    <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ note.note }}</p>
                                                </div>
                                            </div>
                                            <input type="text" class="form-input text-xs" :placeholder="$t('purchase_orders_page.create_modal.placeholders.item_notes')" v-model="item.notes">
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
    
    // Importar Multiselect
    import Multiselect from '@suadelabs/vue3-multiselect';
    import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

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
        
        return hasValidItems;
    });

    // Methods
    const handleQuantityChange = (index: number, quantity: any) => {
        if (quantity === '' || quantity === null) return;

        if (quantity < 0) {
            props.params.items[index].quantity = 0;
        }
    };

    const getMaxQuantity = (productId: number | null): number => {
        if (!productId) return 0;
        const product = props.products.find(p => p.id === productId);
        return product?.stock || 0;
    };

    const getProductInfo = (productId: number | null) => {
        if (!productId) return null;
        return props.products.find(product => product.id === productId) || null;
    };

    const getQuantityValidation = (productId: number | null, quantity: number) => {
        if (!productId) return { hasError: false, message: '' };
        return { hasError: false, message: '' };
    };

    const getStockClass = (productId: number | null): string => {
        if (!productId) return '';
        const product = props.products.find(p => p.id === productId);
        if (!product) return '';
        
        if (product.stock === 0) return 'text-danger';
        if (product.stock <= (product.reorder_point || 0)) return 'text-warning';
        return 'text-success';
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