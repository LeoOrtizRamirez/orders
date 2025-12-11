<template>
    <Transition appear enter-from-class="opacity-0" enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div class="fixed inset-0 z-[999] bg-[rgba(0,0,0,0.2)]" v-if="show">
            <Transition appear enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                <div class="panel absolute top-1/2 left-1/2 w-full max-w-lg -translate-x-1/2 -translate-y-1/2" v-if="show">
                    <div class="flex items-center justify-between bg-[#fbfbfb] px-5 py-3 dark:bg-[#121c2c]">
                        <h5 class="text-lg font-bold">{{ t('purchase_orders_page.split_modal.title') }}</h5>
                        <button type="button" class="text-white-dark hover:text-dark" @click="emit('close')">
                            <svg>...</svg>
                        </button>
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
                                <label for="notes">{{ t('purchase_orders_page.split_modal.fields.notes') }}</label>
                                <textarea id="notes" rows="3" :placeholder="t('purchase_orders_page.split_modal.placeholders.notes')" class="form-textarea"
                                    :class="[errors.notes ? 'border-danger' : '']"
                                    v-model="params.notes"
                                ></textarea>
                                <template v-if="errors.notes">
                                    <p class="text-danger mt-1">{{ errors.notes[0] }}</p>
                                </template>
                            </div>

                            <div class="mb-5">
                                <label>{{ t('purchase_orders_page.split_modal.fields.items_to_split') }}</label>
                                <div v-if="order && order.items && order.items.length > 0">
                                    <div v-for="(item, index) in order.items" :key="item.id" class="flex items-center justify-between mb-3 border p-3 rounded">
                                        <div class="flex-grow">
                                            <p class="font-semibold">{{ item.product.name }} ({{ item.quantity }} {{ t('purchase_orders_page.split_modal.available') }})</p>
                                        </div>
                                        <div class="w-1/3 ml-4">
                                            <input type="number" :placeholder="t('purchase_orders_page.split_modal.placeholders.quantity_to_split')" class="form-input"
                                                v-model.number="params.items[index].quantity"
                                                :min="0"
                                                :max="item.quantity"
                                                @input="validateQuantity(index, item.quantity)"
                                            />
                                            <template v-if="errors[`items.${index}.quantity`]">
                                                <p class="text-danger mt-1">{{ errors[`items.${index}.quantity`][0] }}</p>
                                            </template>
                                        </div>
                                    </div>
                                    <template v-if="errors.items">
                                        <p class="text-danger mt-1">{{ errors.items[0] }}</p>
                                    </template>
                                </div>
                                <div v-else class="text-center text-gray-500">
                                    {{ t('purchase_orders_page.split_modal.no_items') }}
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
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<script lang="ts" setup>
    import { ref, watch, reactive } from 'vue';
    import { useI18n } from 'vue-i18n';

    const { t } = useI18n();

    const emit = defineEmits(['close', 'split']);
    const props = defineProps<{
        show: boolean;
        order: any; // The purchase order to split
        errors: any;
        saving: boolean;
    }>();

    const defaultParams = {
        expected_delivery_date: '',
        notes: '',
        items: [] as { item_id: number; quantity: number }[],
    };

    const params = reactive<{
        expected_delivery_date: string;
        notes: string;
        items: { item_id: number; quantity: number }[];
    }>({ ...defaultParams });

    // Watch for changes in the parent order prop to reset params and initialize items
    watch(() => props.order, (newOrder) => {
        if (newOrder && newOrder.items) {
            params.items = newOrder.items.map((item: any) => ({
                item_id: item.id,
                quantity: item.quantity,
            }));
            params.expected_delivery_date = '';
            params.notes = '';
        } else {
            params.items = [];
        }
    }, { immediate: true });

    watch(() => props.show, (newVal) => {
        if (!newVal) {
            // Reset params when modal is closed
            Object.assign(params, defaultParams);
            params.items = []; // Ensure items array is cleared
        }
    });

    const validateQuantity = (index: number, maxQuantity: number) => {
        if (params.items[index].quantity < 0) {
            params.items[index].quantity = 0;
        } else if (params.items[index].quantity > maxQuantity) {
            params.items[index].quantity = maxQuantity;
        }
    };

    const handleSplit = () => {
        // Filter out items with quantity 0 before emitting
        const itemsToSplit = params.items.filter(item => item.quantity > 0);
        
        if (itemsToSplit.length === 0) {
            // Optionally show an error if no items are selected for split
            alert(t('purchase_orders_page.split_modal.alerts.no_items_selected'));
            return;
        }

        emit('split', {
            expected_delivery_date: params.expected_delivery_date,
            notes: params.notes,
            items: itemsToSplit,
        });
    };
</script>

<style scoped>
/* You can add specific styles for the modal here if needed */
</style>
