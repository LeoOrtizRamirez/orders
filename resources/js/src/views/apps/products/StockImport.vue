<template>
    <div class="container mx-auto p-4">
        <div class="mb-4">
            <h1 class="text-3xl font-bold">Importar Stock de Productos</h1>
            <p class="text-gray-500">Sube un archivo CSV con las columnas 'sku' y 'stock' para actualizar el inventario masivamente.</p>
        </div>

        <!-- Alertas -->
        <div v-if="errorMessage" class="alert alert-danger mb-4">
            {{ errorMessage }}
        </div>
        <div v-if="successMessage" class="alert alert-success mb-4">
            {{ successMessage }}
        </div>

        <div class="panel">
            <div class="panel-body">
                <div class="w-full">
                    <div class="mb-4">
                        <label for="file-upload" class="block text-sm font-medium text-gray-700">Archivo CSV</label>
                        <input
                            id="file-upload"
                            type="file"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"
                            @change="handleFileChange"
                            accept=".csv,.txt"
                        />
                    </div>

                    <button
                        type="button"
                        class="btn btn-primary"
                        :disabled="!file || loading"
                        @click="uploadFile"
                    >
                        <span v-if="loading" class="animate-spin border-2 border-white border-l-transparent rounded-full w-5 h-5 ltr:mr-4 rtl:ml-4 inline-block align-middle"></span>
                        <span v-else>Importar Stock</span>
                    </button>
                </div>

                <!-- Resultados de la importación -->
                <div v-if="results" class="mt-6">
                    <h2 class="text-xl font-bold">Resultados de la Importación</h2>
                    <ul class="list-disc list-inside mt-2">
                        <li><span class="font-semibold">Actualizados:</span> {{ results.updated }}</li>
                        <li><span class="font-semibold">Fallidos:</span> {{ results.failed }}</li>
                    </ul>

                    <div v-if="results.errors && results.errors.length > 0" class="mt-4">
                        <h3 class="font-semibold">Detalles de errores:</h3>
                        <div class="max-h-60 overflow-y-auto">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                <li v-for="(error, index) in results.errors" :key="index">
                                    Fila: {{ JSON.stringify(error.row) }} - Error: {{ error.error }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import { useMeta } from '@/composables/use-meta';
import axios from 'axios';

useMeta({ title: 'Importar Stock de Productos' });

const file = ref<File | null>(null);
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const results = ref<{ updated: number; failed: number; errors: any[] } | null>(null);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        file.value = target.files[0];
    }
};

const uploadFile = async () => {
    if (!file.value) return;

    loading.value = true;
    errorMessage.value = '';
    successMessage.value = '';
    results.value = null;

    const formData = new FormData();
    formData.append('file', file.value);

    try {
        const response = await axios.post('/api/products/import/stock', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        
        if (response.data.success) {
            successMessage.value = response.data.message || 'Importación completada con éxito.';
            results.value = response.data;
        } else {
            errorMessage.value = response.data.message || 'Ocurrió un error durante la importación.';
            if (response.data.errors) {
                results.value = { updated: 0, failed: response.data.errors.length, errors: response.data.errors };
            }
        }
    } catch (error: any) {
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
            if (error.response.data.errors) {
                const failedCount = Object.keys(error.response.data.errors).length;
                results.value = { updated: 0, failed: failedCount, errors: [{row: 'N/A', error: JSON.stringify(error.response.data.errors)}] };
            }
        } else {
            errorMessage.value = 'Ocurrió un error inesperado al conectar con el servidor.';
        }
    } finally {
        loading.value = false;
    }
};
</script>
