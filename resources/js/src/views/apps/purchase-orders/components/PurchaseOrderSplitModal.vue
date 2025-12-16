<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="emit('close')" class="relative z-[51]">
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
                                @click="emit('close')"
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
                            <div class="text-lg font-bold bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3 ltr:pr-[50px] rtl:pl-[50px]">
                                {{ t('purchase_orders_page.split_modal.title') }}
                            </div>
                            <div class="p-5">
                                <form @submit.prevent="handleSplit">
                                    <div class="mb-5">
                                        <label for="expectedDeliveryDate">{{ t('purchase_orders_page.split_modal.fields.expected_delivery_date') }}</label>
                                        <input id="expectedDeliveryDate" type="date" :placeholder="t('purchase_orders_page.split_modal.placeholders.expected_delivery_date')" class="form-input"
                                            :class="[errors.expected_delivery_date ? 'border-danger' : '']"
                                            v-model="params.expected_delivery_date"
                                        />
                                        <template v-if="errors.expected_delivery_date">
                                            <p class="text-danger mt-1">{{ errors.expected_delivery_date[0] }}</p>
                                        </template>
                                    </div>

                                    <div class="mb-5">
                                        <label>{{ t('purchase_orders_page.split_modal.fields.items_to_split') }}</label>
                                        <div v-if="params.items.length > 0">
                                            <div v-for="(item, index) in params.items" :key="item.item_id" class="flex flex-col mb-3 border p-3 rounded">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex-grow">
                                                        <p class="font-semibold">{{ item.product_name }} ({{ item.original_quantity }} {{ t('purchase_orders_page.split_modal.available') }})</p>
                                                    </div>
                                                    <div class="w-1/3 ml-4 flex gap-2">
                                                        <input type="number" :placeholder="t('purchase_orders_page.split_modal.placeholders.quantity_to_split')" class="form-input"
                                                            v-model.number="item.quantity"
                                                            :min="0"
                                                            :max="item.original_quantity"
                                                            @input="validateQuantity(index, item.original_quantity)"
                                                        />
                                                        <button type="button" class="btn btn-outline-danger btn-sm p-2" @click="removeExistingItem(index)">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- Note input for existing item -->
                                                <div>
                                                    <input type="text" class="form-input text-xs" :placeholder="t('purchase_orders_page.create_modal.placeholders.item_notes')" v-model="item.notes">
                                                </div>
                                                <template v-if="errors[`items.${index}.quantity`]">
                                                    <p class="text-danger mt-1 w-full">{{ errors[`items.${index}.quantity`][0] }}</p>
                                                </template>
                                            </div>
                                            <template v-if="errors.items">
                                                <p class="text-danger mt-1">{{ errors.items[0] }}</p>
                                            </template>
                                        </div>
                                        <div v-else class="text-center text-gray-500">
                                            {{ t('purchase_orders_page.split_modal.no_items') }}
                                        </div>
                                    </div>

                                    <!-- Add New Items Section -->
                                    <div class="mb-5">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="font-bold">Agregar Productos Extra</label>
                                            <button type="button" class="btn btn-primary btn-sm" @click="addNewItem">
                                                + Agregar Item
                                            </button>
                                        </div>
                                        <div v-if="params.newItems.length > 0">
                                            <div v-for="(newItem, index) in params.newItems" :key="index" class="flex flex-col mb-3 border p-3 rounded gap-3">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="flex-grow">
                                                        <select v-model="newItem.product_id" class="form-select w-full" required>
                                                            <option :value="null">Seleccionar Producto</option>
                                                            <option v-for="product in products" :key="product.id" :value="product.id">
                                                                {{ product.name }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="w-24">
                                                        <input type="number" class="form-input" v-model.number="newItem.quantity" min="1" placeholder="Cant." required>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" @click="removeNewItem(index)">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                    </button>
                                                </div>
                                                <!-- Note input for new item -->
                                                <div>
                                                    <input type="text" class="form-input text-xs" :placeholder="t('purchase_orders_page.create_modal.placeholders.item_notes')" v-model="newItem.notes">
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="text-center text-gray-500 text-sm">
                                            No hay productos extra agregados.
                                        </div>
                                    </div>

                                    <div class="mt-8 flex items-center justify-end">
                                        <button type="button" class="btn btn-outline-danger" @click="emit('close')">
                                            {{ t('purchase_orders_page.split_modal.buttons.cancel') }}
                                        </button>
                                        <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4" :disabled="saving">
                                            <span v-if="saving">{{ t('purchase_orders_page.split_modal.buttons.splitting') }}</span>
                                            <span v-else>{{ t('purchase_orders_page.split_modal.buttons.split') }}</span>
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
    import { ref, watch, reactive } from 'vue';
    import { useI18n } from 'vue-i18n';
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';

    const { t } = useI18n();

    const emit = defineEmits(['close', 'split']);
    const props = defineProps<{
        show: boolean;
        order: any; // The purchase order to split
        products: any[]; // List of available products
        errors: any;
        saving: boolean;
    }>();

    const defaultParams = {
        expected_delivery_date: '',
        items: [] as { item_id: number; quantity: number; product_name: string; original_quantity: number; notes: string }[],
        newItems: [] as { product_id: number | null; quantity: number; notes: string }[],
    };

    const params = reactive<{
        expected_delivery_date: string;
        items: { item_id: number; quantity: number; product_name: string; original_quantity: number; notes: string }[];
        newItems: { product_id: number | null; quantity: number; notes: string }[];
    }>({ ...defaultParams });

    // Watch for changes in the parent order prop to reset params and initialize items
    watch(() => props.order, (newOrder) => {
        if (newOrder && newOrder.items) {
            params.items = newOrder.items.map((item: any) => ({
                item_id: item.id,
                quantity: item.quantity,
                product_name: item.product?.name || 'Unknown Product',
                original_quantity: item.quantity,
                notes: ''
            }));
            params.expected_delivery_date = '';
            params.newItems = [];
        } else {
            params.items = [];
            params.newItems = [];
        }
    }, { immediate: true });

    watch(() => props.show, (newVal) => {
        if (!newVal) {
            // Reset params when modal is closed
            Object.assign(params, defaultParams);
            params.items = []; 
            params.newItems = [];
        }
    });

    const validateQuantity = (index: number, maxQuantity: number) => {
        if (params.items[index].quantity < 0) {
            params.items[index].quantity = 0;
        } else if (params.items[index].quantity > maxQuantity) {
            params.items[index].quantity = maxQuantity;
        }
    };

    const removeExistingItem = (index: number) => {
        params.items.splice(index, 1);
    };

    const addNewItem = () => {
        params.newItems.push({ product_id: null, quantity: 1, notes: '' });
    };

    const removeNewItem = (index: number) => {
        params.newItems.splice(index, 1);
    };

    const handleSplit = () => {
        // Filter out existing items with quantity 0
        const itemsToSplit = params.items.filter(item => item.quantity > 0);
        
        // Validate and filter new items
        const newItemsToAdd = params.newItems.filter(item => item.product_id && item.quantity > 0);

        if (itemsToSplit.length === 0 && newItemsToAdd.length === 0) {
            alert(t('purchase_orders_page.split_modal.alerts.no_items_selected'));
            return;
        }

        // Combine both lists
        // Note: Backend expects basic structure, stripping UI helpers
        const formattedExistingItems = itemsToSplit.map(item => ({
            item_id: item.item_id,
            quantity: item.quantity,
            notes: item.notes
        }));

        const allItems = [...formattedExistingItems, ...newItemsToAdd];

        emit('split', {
            expected_delivery_date: params.expected_delivery_date,
            items: allItems,
        });
    };
</script>