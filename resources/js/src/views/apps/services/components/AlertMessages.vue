<template>
    <div>
        <!-- Alerta de Error -->
        <div 
            v-if="errorMessage" 
            class="mt-4 alert alert-danger alert-dismissible fade show" 
            role="alert"
        >
            <i class="ri-error-warning-line me-2"></i>
            {{ errorMessage }}
            <button 
                type="button" 
                class="btn-close" 
                @click="$emit('clear-error')"
            ></button>
        </div>

        <!-- Alerta de Éxito -->
        <div 
            v-if="successMessage" 
            class="mt-4 alert alert-success alert-dismissible fade show" 
            role="alert"
        >
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ successMessage }}
            <button 
                type="button" 
                class="btn-close" 
                @click="$emit('clear-success')"
            ></button>
        </div>
    </div>
</template>

<script lang="ts" setup>
    interface Props {
        errorMessage: string;
        successMessage: string;
    }

    interface Emits {
        (e: 'clear-error'): void;
        (e: 'clear-success'): void;
    }

    defineProps<Props>();
    defineEmits<Emits>();
</script>