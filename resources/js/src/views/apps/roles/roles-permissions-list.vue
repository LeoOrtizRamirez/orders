<template>
    <div>
        <div class="panel px-0 pb-1.5 border-[#e0e6ed] dark:border-[#1b2e4b]">
            <div class="mb-4.5 px-5 flex md:items-center md:flex-row flex-col gap-5">
                <div class="ltr:ml-auto rtl:mr-auto">
                    <input v-model="search" type="text" class="form-input" placeholder="Buscar permisos..." />
                </div>
            </div>

            <!-- Matriz de Roles y Permisos -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider sticky left-0 z-10">
                                Rol / Permiso
                            </th>
                            <th 
                                v-for="permission in filteredPermissions" 
                                :key="permission.id"
                                class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                <div class="flex flex-col items-center">
                                    <span class="text-xs">{{ permission.name }}</span>
                                    <span class="text-[10px] text-gray-400">{{ permission.group || 'Sin grupo' }}</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="role in roles" :key="role.id">
                            <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white dark:bg-gray-800 z-10">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center text-white font-bold mr-3">
                                        {{ role.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ role.name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ role.users_count }} usuarios
                                            <span class="mx-1">•</span>
                                            <span :class="role.is_system ? 'text-orange-500' : 'text-blue-500'">
                                                {{ role.is_system ? 'Sistema' : 'Personalizado' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td 
                                v-for="permission in filteredPermissions" 
                                :key="permission.id"
                                class="px-4 py-4 whitespace-nowrap text-center"
                            >
                                <div class="relative">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            class="form-switch"
                                            :checked="hasPermission(role, permission)"
                                            :disabled="!authStore.can('manage roles') || role.is_system || loadingStates[`${role.id}-${permission.id}`]"
                                            @change="togglePermission(role, permission, $event.target.checked)"
                                        />
                                        <span class="ml-2 text-sm" :class="hasPermission(role, permission) ? 'text-green-600' : 'text-gray-400'">
                                            {{ hasPermission(role, permission) ? 'Sí' : 'No' }}
                                        </span>
                                    </label>
                                    <div v-if="loadingStates[`${role.id}-${permission.id}`]" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 dark:bg-gray-800 dark:bg-opacity-80">
                                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-10">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && roles.length === 0" class="text-center py-10">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay roles</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza creando algunos roles para gestionar permisos.</p>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted, computed } from 'vue';
    import { useAuthStore } from '@/stores/auth';
    import axios from 'axios';
    import Swal from 'sweetalert2';

    const authStore = useAuthStore();
    const search = ref('');
    const loading = ref(false);
    const loadingStates = ref<Record<string, boolean>>({});

    // Datos
    const roles = ref<any[]>([]);
    const permissions = ref<any[]>([]);

    // Permisos filtrados
    const filteredPermissions = computed(() => {
        if (!search.value) return permissions.value;
        return permissions.value.filter(permission => 
            permission.name.toLowerCase().includes(search.value.toLowerCase()) ||
            (permission.description && permission.description.toLowerCase().includes(search.value.toLowerCase())) ||
            (permission.group && permission.group.toLowerCase().includes(search.value.toLowerCase()))
        );
    });

    // Cargar datos
    const fetchData = async () => {
        loading.value = true;
        try {
            const [rolesResponse, permissionsResponse] = await Promise.all([
                axios.get('/api/admin/roles'),
                axios.get('/api/admin/permissions')
            ]);
            
            roles.value = rolesResponse.data.data;
            permissions.value = permissionsResponse.data.data;
        } catch (error) {
            console.error('Error fetching data:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
        } finally {
            loading.value = false;
        }
    };

    // Verificar si un rol tiene un permiso
    const hasPermission = (role: any, permission: any) => {
        return role.permissions?.some((p: any) => p.id === permission.id);
    };

    // Alternar permiso - Ahora envía array de IDs al backend
    const togglePermission = async (role: any, permission: any, enabled: boolean) => {
        if (!authStore.can('manage roles') || role.is_system) {
            return;
        }

        const key = `${role.id}-${permission.id}`;
        loadingStates.value[key] = true;

        try {
            // Obtener permisos actuales del rol (IDs)
            const currentPermissions = role.permissions?.map((p: any) => p.id) || [];
            
            // Crear nuevo array de permisos (IDs)
            let newPermissions = [...currentPermissions];
            
            if (enabled) {
                if (!newPermissions.includes(permission.id)) {
                    newPermissions.push(permission.id);
                }
            } else {
                newPermissions = newPermissions.filter(id => id !== permission.id);
            }

            // Actualizar rol enviando array de IDs
            const response = await axios.put(`/api/admin/roles/${role.id}`, {
                permissions: newPermissions // Array de IDs numéricos
            });

            // Actualizar el rol localmente con la respuesta del servidor
            const updatedRole = response.data.data;
            const roleIndex = roles.value.findIndex(r => r.id === role.id);
            if (roleIndex !== -1) {
                roles.value[roleIndex] = updatedRole;
            }

            // Mostrar feedback visual
            Swal.fire({
                icon: 'success',
                title: '¡Permiso actualizado!',
                text: `El permiso "${permission.name}" ha sido ${enabled ? 'habilitado' : 'deshabilitado'} para el rol "${role.name}"`,
                timer: 5000,
                showConfirmButton: false,
                toast: true,
                position: 'top'
            });

        } catch (error: any) {
            console.error('Error updating permission:', error);
            
            // Revertir visualmente el cambio en la UI
            const roleIndex = roles.value.findIndex(r => r.id === role.id);
            if (roleIndex !== -1) {
                // Forzar re-renderizado manteniendo los datos originales
                roles.value = [...roles.value];
            }

            let errorMessage = 'No se pudo actualizar el permiso';
            if (error.response?.data?.error) {
                errorMessage = error.response.data.error;
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                timer: 5000,
                showConfirmButton: false,
                toast: true,
                position: 'top'
            });
        } finally {
            loadingStates.value[key] = false;
        }
    };

    onMounted(() => {
        fetchData();
    });
</script>

<style scoped>
.sticky {
    position: sticky;
}

.dark .bg-gray-800 {
    background-color: #1f2937;
}

.form-switch {
    position: relative;
    width: 44px;
    height: 24px;
    appearance: none;
    background: #d1d5db;
    border-radius: 12px;
    transition: all 0.3s;
    cursor: pointer;
}

.form-switch:checked {
    background: #3b82f6;
}

.form-switch:before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    top: 2px;
    left: 2px;
    transition: all 0.3s;
}

.form-switch:checked:before {
    left: 22px;
}

.form-switch:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.relative {
    position: relative;
}
</style>