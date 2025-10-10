<template>
    <div>
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="text-xl">{{ $t('services_page.title') }}</h2>
            <div class="flex sm:flex-row flex-col sm:items-center sm:gap-3 gap-4 w-full sm:w-auto">
                <div class="flex gap-3">
                    <div>
                        <button 
                            v-if="authStore.can('create services')"
                            type="button" 
                            class="btn btn-primary" 
                            @click="editService()"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
                                <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" stroke="currentColor" stroke-width="1.5"/>
                                <path opacity="0.5" d="M8 12H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path opacity="0.5" d="M12 16V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            {{ $t('services_page.add_service_btn') }}
                        </button>
                    </div>
                    <div>
                        <button
                            type="button"
                            class="btn btn-outline-primary p-2"
                            :class="{ 'bg-primary text-white': displayType === 'list' }"
                            @click="displayType = 'list'"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path d="M2 5.5L3.21429 7L7.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    opacity="0.5"
                                    d="M2 12.5L3.21429 14L7.5 10"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path d="M2 19.5L3.21429 21L7.5 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M22 19L12 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path opacity="0.5" d="M22 12L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M22 5L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <button
                            type="button"
                            class="btn btn-outline-primary p-2"
                            :class="{ 'bg-primary text-white': displayType === 'grid' }"
                            @click="displayType = 'grid'"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path
                                    opacity="0.5"
                                    d="M2.5 6.5C2.5 4.61438 2.5 3.67157 3.08579 3.08579C3.67157 2.5 4.61438 2.5 6.5 2.5C8.38562 2.5 9.32843 2.5 9.91421 3.08579C10.5 3.67157 10.5 4.61438 10.5 6.5C10.5 8.38562 10.5 9.32843 9.91421 9.91421C9.32843 10.5 8.38562 10.5 6.5 10.5C4.61438 10.5 3.67157 10.5 3.08579 9.91421C2.5 9.32843 2.5 8.38562 2.5 6.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                                <path
                                    opacity="0.5"
                                    d="M13.5 17.5C13.5 15.6144 13.5 14.6716 14.0858 14.0858C14.6716 13.5 15.6144 13.5 17.5 13.5C19.3856 13.5 20.3284 13.5 20.9142 14.0858C21.5 14.6716 21.5 15.6144 21.5 17.5C21.5 19.3856 21.5 20.3284 20.9142 20.9142C20.3284 21.5 19.3856 21.5 17.5 21.5C15.6144 21.5 14.6716 21.5 14.0858 20.9142C13.5 20.3284 13.5 19.3856 13.5 17.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                                <path
                                    d="M2.5 17.5C2.5 15.6144 2.5 14.6716 3.08579 14.0858C3.67157 13.5 4.61438 13.5 6.5 13.5C8.38562 13.5 9.32843 13.5 9.91421 14.0858C10.5 14.6716 10.5 15.6144 10.5 17.5C10.5 19.3856 10.5 20.3284 9.91421 20.9142C9.32843 21.5 8.38562 21.5 6.5 21.5C4.61438 21.5 3.67157 21.5 3.08579 20.9142C2.5 20.3284 2.5 19.3856 2.5 17.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                                <path
                                    d="M13.5 6.5C13.5 4.61438 13.5 3.67157 14.0858 3.08579C14.6716 2.5 15.6144 2.5 17.5 2.5C19.3856 2.5 20.3284 2.5 20.9142 3.08579C21.5 3.67157 21.5 4.61438 21.5 6.5C21.5 8.38562 21.5 9.32843 20.9142 9.91421C20.3284 10.5 19.3856 10.5 17.5 10.5C15.6144 10.5 14.6716 10.5 14.0858 9.91421C13.5 9.32843 13.5 8.38562 13.5 6.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="relative">
                    <input
                        type="text"
                        :placeholder="$t('services_page.search_placeholder')"
                        class="form-input py-2 ltr:pr-11 rtl:pl-11 peer"
                        v-model="searchService"
                        @keyup="searchServices"
                    />
                    <div class="absolute ltr:right-[11px] rtl:left-[11px] top-1/2 -translate-y-1/2 peer-focus:text-primary">
                        <svg class="mx-auto" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5" opacity="0.5"></circle>
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        <div v-if="errorMessage" class="mt-4 alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            {{ errorMessage }}
            <button type="button" class="btn-close" @click="errorMessage = ''"></button>
        </div>

        <div v-if="successMessage" class="mt-4 alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ successMessage }}
            <button type="button" class="btn-close" @click="successMessage = ''"></button>
        </div>

        <div class="mt-5 panel p-0 border-0 overflow-hidden">
            <template v-if="displayType === 'list'">
                <div class="table-responsive">
                    <table class="table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ $t('services_page.name') }}</th>
                                <th>{{ $t('services_page.descripcion') }}</th>
                                <th>{{ $t('services_page.price') }}</th>
                                <th>{{ $t('services_page.videos') }}</th>
                                <th>{{ $t('services_page.status') }}</th>
                                <th class="!text-center">{{ $t('services_page.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="loading">
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="animate-spin border-2 border-primary border-t-transparent rounded-full w-6 h-6 mx-auto"></div>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <template v-for="service in filteredServicesList" :key="service.id">
                                    <tr>
                                        <td>
                                            <div class="flex items-center w-max">
                                                <div
                                                    v-if="service.icon"
                                                    class="grid place-content-center h-8 w-8 ltr:mr-2 rtl:ml-2 rounded-full text-lg"
                                                    :style="{ backgroundColor: service.color + '20', color: service.color }"
                                                >
                                                    {{ service.icon }}
                                                </div>
                                                <div class="font-semibold">{{ service.name }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="max-w-xs truncate" :title="service.description">
                                                {{ service.description }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="max-w-xs truncate" :title="service.price">
                                                {{ moneyFormat(service.price) }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info/10 text-info">
                                                {{ service.videos_count || 0 }} {{ $t('services_page.videos') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span 
                                                class="badge" 
                                                :class="service.is_active ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'"
                                            >
                                                {{ service.is_active ? $t('services_page.active') : $t('services_page.inactive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="flex gap-2 items-center justify-center">
                                                <!-- Botón para gestionar vídeos -->
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-info" 
                                                    @click="manageVideos(service)"
                                                    :title="$t('services_page.manage_videos_tooltip')"
                                                >
                                                    <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $t('services_page.videos_btn') }}
                                                </button>

                                                <button 
                                                    v-if="authStore.can('edit services')"
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    @click="editService(service)"
                                                >
                                                    {{ $t('services_page.edit_btn') }}
                                                </button>

                                                <button 
                                                    v-if="authStore.can('delete services')"
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    @click="deleteService(service)"
                                                    :disabled="(service.videos_count || 0) > 0"
                                                    :title="(service.videos_count || 0) > 0 ? $t('services_page.cannot_delete_tooltip') : ''"
                                                >
                                                    {{ $t('services_page.delete_btn') }}
                                                </button>

                                                <button 
                                                    v-if="authStore.can('edit services')"
                                                    type="button" 
                                                    class="btn btn-sm" 
                                                    :class="service.is_active ? 'btn-outline-warning' : 'btn-outline-success'"
                                                    @click="toggleServiceStatus(service)"
                                                >
                                                    {{ service.is_active ? $t('services_page.deactivate_btn') : $t('services_page.activate_btn') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>

        <template v-if="displayType === 'grid'">
            <div class="grid 2xl:grid-cols-4 xl:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6 w-full mt-5">
                <template v-if="loading">
                    <div v-for="i in 8" :key="'skeleton-'+i" class="bg-white dark:bg-[#1c232f] rounded-md overflow-hidden text-center shadow relative h-64">
                        <div class="animate-pulse h-full flex flex-col">
                            <div class="bg-gray-300 h-32 rounded-t-md"></div>
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="h-4 bg-gray-300 rounded"></div>
                                    <div class="h-3 bg-gray-300 rounded"></div>
                                    <div class="h-3 bg-gray-300 rounded w-3/4 mx-auto"></div>
                                </div>
                                <div class="flex gap-2 mt-4">
                                    <div class="h-8 bg-gray-300 rounded flex-1"></div>
                                    <div class="h-8 bg-gray-300 rounded flex-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <template v-for="service in filteredServicesList" :key="service.id">
                        <div class="bg-white dark:bg-[#1c232f] rounded-md overflow-hidden text-center shadow relative">
                            <div 
                                class="rounded-t-md p-6 pb-0 relative h-32"
                                :style="{ 
                                    backgroundColor: service.color + '20',
                                    backgroundImage: 'url(/assets/images/service-bg.png)',
                                    backgroundSize: 'cover',
                                    backgroundPosition: 'center'
                                }"
                            >
                                <div 
                                    class="grid place-content-center h-16 w-16 mx-auto rounded-full text-3xl mb-4"
                                    :style="{ backgroundColor: service.color + '40', color: service.color }"
                                >
                                    {{ service.icon }}
                                </div>
                            </div>
                            <div class="px-6 pb-20 -mt-8 relative">
                                <div class="shadow-md bg-white dark:bg-gray-900 rounded-md px-4 py-6">
                                    <div class="text-xl font-semibold mb-2">{{ service.name }}</div>
                                    <div class="text-white-dark text-sm min-h-[40px] line-clamp-2">{{ service.description }}</div>
                                    
                                    <div class="mt-4 flex justify-center items-center gap-4">
                                        <span class="text-lg font-bold text-primary">${{ service.price }}</span>
                                        <span class="text-sm text-gray-500">•</span>
                                        <span class="text-sm text-gray-600">{{ service.duration_minutes }} min</span>
                                    </div>

                                    <div class="mt-4 flex justify-center items-center gap-4">
                                        <span class="badge bg-info/10 text-info">
                                            {{ service.videos_count || 0 }} {{ $t('services_page.videos') }}
                                        </span>
                                        <span 
                                            class="badge" 
                                            :class="service.is_active ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'"
                                        >
                                            {{ service.is_active ? $t('services_page.active') : $t('services_page.inactive') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex gap-2 absolute bottom-0 w-full ltr:left-0 rtl:right-0 p-4">
                                <!-- Botón principal para gestionar vídeos -->
                                <button 
                                    type="button" 
                                    class="btn btn-info flex-1" 
                                    @click="manageVideos(service)"
                                >
                                    <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $t('services_page.manage_videos') }}
                                </button>
                            </div>
                            
                            <!-- Botones secundarios en esquina superior derecha -->
                            <div class="absolute top-4 right-4 flex gap-1">
                                <button 
                                    v-if="authStore.can('edit services')"
                                    type="button" 
                                    class="btn btn-sm btn-outline-primary p-1"
                                    @click="editService(service)"
                                    :title="$t('services_page.edit_tooltip')"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                
                                <button 
                                    v-if="authStore.can('edit services')"
                                    type="button" 
                                    class="btn btn-sm" 
                                    :class="service.is_active ? 'btn-outline-warning' : 'btn-outline-success'"
                                    @click="toggleServiceStatus(service)"
                                    :title="service.is_active ? $t('services_page.deactivate_tooltip') : $t('services_page.activate_tooltip')"
                                >
                                    {{ service.is_active ? '🔴' : '🟢' }}
                                </button>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
        </template>

        <!-- Modal para agregar/editar servicio -->
        <TransitionRoot appear :show="addServiceModal" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-[51]">
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
                            <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-2xl text-black dark:text-white-dark">
                                <button
                                    type="button"
                                    class="absolute top-4 ltr:right-4 rtl:left-4 text-gray-400 hover:text-gray-800 dark:hover:text-gray-600 outline-none"
                                    @click="closeModal"
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
                                    {{ params.id ? $t('services_page.modal_edit_title') : $t('services_page.modal_add_title') }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="saveService">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="mb-5">
                                                <label for="name">{{ $t('services_page.name') }} *</label>
                                                <input id="name" type="text" placeholder="Ej: Asesoría Financiera" class="form-input" v-model="params.name" required />
                                                <div v-if="errors.name" class="text-danger mt-1">{{ errors.name[0] }}</div>
                                            </div>
                                            <div class="mb-5">
                                                <label for="slug">{{ $t('services_page.slug') }} *</label>
                                                <input id="slug" type="text" placeholder="Ej: asesoria-financiera" class="form-input" v-model="params.slug" required />
                                                <div v-if="errors.slug" class="text-danger mt-1">{{ errors.slug[0] }}</div>
                                            </div>
                                        </div>

                                        <div class="mb-5">
                                            <label for="description">{{ $t('services_page.descripcion') }}</label>
                                            <textarea id="description" placeholder="Descripción del servicio..." class="form-textarea min-h-[80px]" v-model="params.description"></textarea>
                                            <div v-if="errors.description" class="text-danger mt-1">{{ errors.description[0] }}</div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="mb-5">
                                                <label for="price">{{ $t('services_page.price') }} *</label>
                                                <input id="price" type="number" step="0.01" min="0" placeholder="0.00" class="form-input" v-model="params.price" required />
                                                <div v-if="errors.price" class="text-danger mt-1">{{ errors.price[0] }}</div>
                                            </div>
                                            <div class="mb-5">
                                                <label for="duration_minutes">{{ $t('services_page.time') }} *</label>
                                                <input id="duration_minutes" type="number" min="1" placeholder="60" class="form-input" v-model="params.duration_minutes" required />
                                                <div v-if="errors.duration_minutes" class="text-danger mt-1">{{ errors.duration_minutes[0] }}</div>
                                            </div>
                                            <div class="mb-5">
                                                <label for="order">{{ $t('services_page.order') }}</label>
                                                <input id="order" type="number" min="0" placeholder="0" class="form-input" v-model="params.order" />
                                                <div v-if="errors.order" class="text-danger mt-1">{{ errors.order[0] }}</div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="mb-5">
                                                <label for="icon">{{ $t('services_page.icon') }}</label>
                                                <input id="icon" type="text" placeholder="Ej: 💰" class="form-input" v-model="params.icon" maxlength="2" />
                                                <div class="text-xs text-gray-500 mt-1">Usa un emoji para representar el servicio</div>
                                                <div v-if="errors.icon" class="text-danger mt-1">{{ errors.icon[0] }}</div>
                                            </div>
                                            <div class="mb-5">
                                                <label for="color">{{ $t('services_page.color') }}</label>
                                                <div class="flex gap-2">
                                                    <input id="color" type="color" class="form-input p-1 h-10" v-model="params.color" />
                                                    <input type="text" placeholder="#10B981" class="form-input flex-1" v-model="params.color" />
                                                </div>
                                                <div v-if="errors.color" class="text-danger mt-1">{{ errors.color[0] }}</div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                                            <div class="mb-5">                                            
                                                <label for="whatsapp_url">{{ $t('services_page.whatsapp_url') }}</label>
                                                <input id="whatsapp_url" type="text" placeholder="Ej: https://wa.me/573232089499" class="form-input" v-model="params.whatsapp_url" />
                                                <div v-if="errors.whatsapp_url" class="text-danger mt-1">{{ errors.whatsapp_url[0] }}</div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="mb-5">
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="checkbox" class="form-checkbox" v-model="params.is_active" />
                                                    <span class="text-white-dark ltr:ml-3 rtl:mr-3">{{ $t('services_page.active_check') }}</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="flex justify-end items-center mt-8">
                                            <button type="button" class="btn btn-outline-danger" @click="closeModal">{{ $t('services_page.cancel_btn') }}</button>
                                            <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4" :disabled="saving">
                                                <span v-if="saving" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4 ltr:mr-2 rtl:ml-2 inline-block"></span>
                                                {{ params.id ? $t('services_page.update_btn') : $t('services_page.add_btn') }}
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
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted, computed } from 'vue';
    import { useRouter } from 'vue-router';
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
    import Swal from 'sweetalert2';
    import { useMeta } from '@/composables/use-meta';
    import { useAuthStore } from '@/stores/auth';
    import axios from 'axios';
    
    useMeta({ title: 'Administración de Servicios' });

    const router = useRouter();
    const authStore = useAuthStore();

    const displayType = ref('list');
    const addServiceModal = ref(false);
    const loading = ref(false);
    const saving = ref(false);
    const errorMessage = ref('');
    const successMessage = ref('');
    const errors = ref({});
    const searchService = ref('');
    
    const defaultParams = ref({
        id: null,
        name: '',
        slug: '',
        description: '',
        price: 0,
        duration_minutes: 60,
        is_active: true,
        order: 0,
        icon: '',
        color: '#3B82F6',
        whatsapp_url: ''
    });
    
    const params = ref({...defaultParams.value});
    const servicesList = ref([]);

    // Computed property for filtered services
    const filteredServicesList = computed(() => {
        if (!searchService.value) {
            return servicesList.value;
        }
        return servicesList.value.filter((service) => 
            service.name.toLowerCase().includes(searchService.value.toLowerCase()) ||
            service.description.toLowerCase().includes(searchService.value.toLowerCase())
        );
    });

    onMounted(() => {
        fetchServices();
    });

    const fetchServices = async () => {
        loading.value = true;
        errorMessage.value = '';
        try {
            const response = await axios.get('/api/services');
            servicesList.value = response.data.data;
        } catch (error) {
            console.error('Error fetching services:', error);
            errorMessage.value = 'Error al cargar los servicios. Por favor, intente nuevamente.';
            showMessage('Error al cargar los servicios.', 'error');
        } finally {
            loading.value = false;
        }
    };

    const searchServices = () => {
        // La búsqueda se maneja con la computed property
    };

    // Nueva función para redireccionar a la gestión de vídeos
    const manageVideos = (service: any) => {
        router.push(`/services/${service.id}/videos`);
    };

    const editService = (service: any = null) => {
        errors.value = {};
        if (service) {
            params.value = {
                id: service.id,
                name: service.name,
                slug: service.slug,
                description: service.description || '',
                price: parseFloat(service.price),
                duration_minutes: service.duration_minutes,
                is_active: service.is_active,
                order: service.order || 0,
                icon: service.icon || '',
                color: service.color || '#3B82F6',
                whatsapp_url: service.whatsapp_url || ''
            };
        } else {
            params.value = {...defaultParams.value};
        }
        addServiceModal.value = true;
    };

    const saveService = async () => {
        saving.value = true;
        errors.value = {};
        errorMessage.value = '';
        successMessage.value = '';

        try {
            if (params.value.id) {
                // Actualizar servicio
                const { data } = await axios.put(`/api/services/${params.value.id}`, params.value);
                const index = servicesList.value.findIndex(service => service.id === params.value.id);
                if (index !== -1) {
                    servicesList.value.splice(index, 1, data);
                }
                showMessage('Servicio actualizado satisfactoriamente!');
            } else {
                // Crear servicio
                const { data } = await axios.post('/api/services', params.value);
                servicesList.value.unshift(data);
                showMessage('Servicio creado satisfactoriamente!');
            }
            addServiceModal.value = false;
        } catch (error: any) {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                errorMessage.value = 'Por favor corrija los errores de validación.';
            } else {
                console.error('Error al guardar servicio:', error);
                errorMessage.value = 'No se pudo guardar el servicio. Inténtalo de nuevo.';
                showMessage('No se pudo guardar el servicio.', 'error');
            }
        } finally {
            saving.value = false;
        }
    };

    const deleteService = async (service: any) => {
        if ((service.videos_count || 0) > 0) {
            showMessage('No se puede eliminar el servicio porque tiene vídeos asociados.', 'error');
            return;
        }

        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, ¡elimínalo!',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/services/${service.id}`);
                servicesList.value = servicesList.value.filter(s => s.id !== service.id);
                showMessage('Servicio eliminado satisfactoriamente!');
            } catch (error: any) {
                console.error('Error eliminando servicio:', error);
                if (error.response?.status === 400) {
                    showMessage(error.response.data.message, 'error');
                } else {
                    showMessage('Error al eliminar el servicio.', 'error');
                }
            }
        }
    };

    const toggleServiceStatus = async (service: any) => {
        try {
            const { data } = await axios.put(`/api/services/${service.id}/toggle-status`);
            const index = servicesList.value.findIndex(s => s.id === service.id);
            if (index !== -1) {
                servicesList.value[index].is_active = data.is_active;
            }
            showMessage(`Servicio ${data.is_active ? 'activado' : 'desactivado'} satisfactoriamente!`);
        } catch (error) {
            console.error('Error cambiando estado del servicio:', error);
            showMessage('Error al cambiar el estado del servicio.', 'error');
        }
    };

    const closeModal = () => {
        addServiceModal.value = false;
        params.value = {...defaultParams.value};
        errors.value = {};
    };

    const showMessage = (msg = '', type = 'success') => {
        if (type === 'success') {
            successMessage.value = msg;
        } else {
            errorMessage.value = msg;
        }
        
        const toast: any = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
            customClass: { container: 'toast' },
        });
        toast.fire({
            icon: type,
            title: msg,
            padding: '10px 20px',
        });
    };

    const moneyFormat = (valor = 0) => {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(valor);
    };
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.min-h-\[40px\] {
    min-height: 40px;
}
</style>