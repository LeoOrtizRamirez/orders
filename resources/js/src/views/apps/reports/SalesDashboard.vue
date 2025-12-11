<template>
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Dashboard de Ventas y Stock</h1>
            <p class="text-gray-500">Visualización de productos vendidos y stock disponible.</p>
        </div>

        <!-- Filtros de fecha -->
        <div class="panel mb-6">
            <div class="panel-body flex items-center space-x-4">
                <div>
                    <label for="startDate">Fecha de Inicio:</label>
                    <input type="date" id="startDate" class="form-input" v-model="filters.startDate" />
                </div>
                <div>
                    <label for="endDate">Fecha de Fin:</label>
                    <input type="date" id="endDate" class="form-input" v-model="filters.endDate" />
                </div>
                <button class="btn btn-primary" @click="fetchReportData">Filtrar</button>
            </div>
        </div>

        <div v-if="loading" class="text-center">Cargando datos...</div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div v-if="reportData" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Gráfico de Ventas a lo largo del tiempo -->
            <div class="panel lg:col-span-2">
                <h2 class="text-xl font-bold mb-4">Cantidad de Productos Vendidos</h2>
                <apexchart height="300" :options="salesChartOptions" :series="salesChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>
            </div>

            <!-- Tabla de Productos más vendidos -->
            <div class="panel">
                <h2 class="text-xl font-bold mb-4">Top 10 Productos Más Vendidos</h2>
                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Producto</th>
                                <th>Total Vendido</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in reportData.top_sold_products" :key="product.id">
                                <td>{{ product.sku }}</td>
                                <td>{{ product.name }}</td>
                                <td>{{ product.total_sold }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabla de Stock de Productos -->
            <div class="panel">
                <h2 class="text-xl font-bold mb-4">Stock de Productos</h2>
                <div class="table-responsive h-96">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Producto</th>
                                <th>Stock Actual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in reportData.product_stock" :key="product.id">
                                <td>{{ product.sku }}</td>
                                <td>{{ product.name }}</td>
                                <td>{{ product.stock }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, computed } from 'vue';
import { useMeta } from '@/composables/use-meta';
import axios from 'axios';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';

useMeta({ title: 'Reporte de Ventas' });

const store = useAppStore();
const loading = ref(true);
const error = ref('');
const reportData = ref<any>(null);

const today = new Date().toISOString().split('T')[0];
const thirtyDaysAgo = new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0];

const filters = ref({
    startDate: thirtyDaysAgo,
    endDate: today,
});

const fetchReportData = async () => {
    loading.value = true;
    error.value = '';
    try {
        const response = await axios.get('/api/reports/sales', {
            params: {
                start_date: filters.value.startDate,
                end_date: filters.value.endDate,
            }
        });
        reportData.value = response.data;
    } catch (err) {
        error.value = 'No se pudieron cargar los datos del reporte.';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchReportData);

const salesChartOptions = computed(() => {
    const isDark = store.theme === 'dark' || store.isDarkMode;
    return {
        chart: {
            type: 'line',
            height: 300,
            toolbar: { show: false },
        },
        colors: ['#194C8D'],
        stroke: {
            width: 2,
            curve: 'smooth',
        },
        xaxis: {
            categories: reportData.value?.sales_over_time.map((d: any) => d.date) || [],
            axisBorder: {
                color: isDark ? '#191e3a' : '#e0e6ed',
            },
        },
        yaxis: {
            labels: {
                formatter: (value: number) => { return value.toFixed(0); },
            },
        },
        grid: {
            borderColor: isDark ? '#191e3a' : '#e0e6ed',
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
        },
    };
});

const salesChartSeries = computed(() => [
    {
        name: 'Cantidad Vendida',
        data: reportData.value?.sales_over_time.map((d: any) => d.total_quantity) || [],
    },
]);
</script>
