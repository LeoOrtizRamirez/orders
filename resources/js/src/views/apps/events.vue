<template>
    <div>
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="text-xl">{{ $t('events_page.events') }}</h2>
            <div class="flex sm:flex-row flex-col sm:items-center sm:gap-3 gap-4 w-full sm:w-auto">
                <div class="flex gap-3">
                    <div>
                        <button 
                            v-if="authStore.can('create events')"
                            type="button" 
                            class="btn btn-outline-success" 
                            @click="toggleImportModal"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
                                <path d="M12.5535 2.49392C12.4114 2.33852 12.2106 2.25 12 2.25C11.7894 2.25 11.5886 2.33852 11.4465 2.49392L7.44648 6.86892C7.16698 7.17462 7.18822 7.64902 7.49392 7.92852C7.79963 8.20802 8.27402 8.18678 8.55352 7.88108L11.25 4.9318V14C11.25 14.4142 11.5858 14.75 12 14.75C12.4142 14.75 12.75 14.4142 12.75 14V4.9318L15.4465 7.88108C15.726 8.18678 16.2004 8.20802 16.5061 7.92852C16.8118 7.64902 16.833 7.17462 16.5535 6.86892L12.5535 2.49392Z" fill="currentColor"/>
                                <path d="M3.75 15C3.75 14.5858 3.41422 14.25 3 14.25C2.58578 14.25 2.25 14.5858 2.25 15V15.0549C2.24998 16.4225 2.24996 17.5248 2.36652 18.3918C2.48754 19.2919 2.74643 20.0497 3.34835 20.6516C3.95027 21.2536 4.70814 21.5125 5.60825 21.6335C6.47522 21.75 7.57754 21.75 8.94513 21.75H15.0549C16.4225 21.75 17.5248 21.75 18.3918 21.6335C19.2919 21.5125 20.0497 21.2536 20.6516 20.6516C21.2536 20.0497 21.5125 19.2919 21.6335 18.3918C21.75 17.5248 21.75 16.4225 21.75 15.0549V15C21.75 14.5858 21.4142 14.25 21 14.25C20.5858 14.25 20.25 14.5858 20.25 15C20.25 16.4354 20.2484 17.4365 20.1469 18.1919C20.0482 18.9257 19.8678 19.3142 19.591 19.591C19.3142 19.8678 18.9257 20.0482 18.1919 20.1469C17.4365 20.2484 16.4354 20.25 15 20.25H9C7.56459 20.25 6.56347 20.2484 5.80812 20.1469C5.07435 20.0482 4.68577 19.8678 4.40901 19.591C4.13225 19.3142 3.9518 18.9257 3.85315 18.1919C3.75159 17.4365 3.75 16.4354 3.75 15Z" fill="currentColor"/>
                            </svg>
                            {{ $t('events_page.import') }}
                        </button>
                    </div>
                    <div>
                        <button 
                            v-if="authStore.can('create events')"
                            type="button" 
                            class="btn btn-primary" 
                            @click="editEvent()"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
                                <path d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999L5.83881 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844L7.47919 20.5844C8.25352 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z" stroke="currentColor" stroke-width="1.5"/>
                                <path opacity="0.5" d="M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19792 21.6777L2.32178 19.8015" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                            {{ $t('events_page.create_event') }}
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
                        :placeholder="$t('events_page.search_placeholder')"
                        class="form-input py-2 ltr:pr-11 rtl:pl-11 peer"
                        v-model="searchEvent"
                        @keyup="searchEvents"
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
                                <th>{{ $t('events_page.title') }}</th>
                                <th>{{ $t('events_page.description') }}</th>
                                <th>{{ $t('events_page.start') }}</th>
                                <th>{{ $t('events_page.end') }}</th>
                                <th>{{ $t('events_page.type') }}</th>
                                <th>{{ $t('events_page.created_by') }}</th>
                                <th class="!text-center">{{ $t('events_page.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="loading">
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="animate-spin border-2 border-primary border-t-transparent rounded-full w-6 h-6 mx-auto"></div>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <template v-for="event in filteredEventsList" :key="event.id">
                                    <tr>
                                        <td>
                                            <div class="flex items-center w-max">
                                                <div class="font-semibold">{{ event.title }}</div>
                                            </div>
                                        </td>
                                        <td>{{ event.description || $t('events_page.no_description') }}</td>
                                        <td>{{ formatDateTime(event.start) }}</td>
                                        <td>{{ formatDateTime(event.end) }}</td>
                                        <td>
                                            <span class="badge" :class="getTypeBadgeClass(event.type)">
                                                {{ getTypeText(event.type) }}
                                            </span>
                                        </td>
                                        <td>{{ event.user ? event.user.name : $t('events_page.not_available') }}</td>
                                        <td>
                                            <div class="flex gap-4 items-center justify-center">
                                                <button 
                                                    v-if="authStore.can('edit events') && canEditEvent(event)"
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    @click="editEvent(event)"
                                                >
                                                    {{ $t('events_page.edit') }}
                                                </button>

                                                <button 
                                                    v-if="authStore.can('delete events') && canEditEvent(event)"
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    @click="deleteEvent(event)"
                                                >
                                                    {{ $t('events_page.delete') }}
                                                </button>

                                                <span 
                                                    v-if="!canEditEvent(event)"
                                                    class="text-xs text-gray-400"
                                                >
                                                    {{ $t('events_page.read_only') }}
                                                </span>
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
            <div class="grid 2xl:grid-cols-4 xl:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6 w-full">
                <template v-if="loading">
                    <div v-for="i in 4" :key="'skeleton-'+i" class="bg-white dark:bg-[#1c232f] rounded-md overflow-hidden text-center shadow relative h-80">
                        <div class="animate-pulse h-full flex flex-col">
                            <div class="bg-gray-300 h-4 mt-4 mx-4 rounded"></div>
                            <div class="bg-gray-300 h-3 mt-2 mx-4 rounded"></div>
                            <div class="bg-gray-300 h-20 mt-4 mx-4 rounded"></div>
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="h-3 bg-gray-300 rounded"></div>
                                    <div class="h-3 bg-gray-300 rounded"></div>
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
                    <template v-for="event in filteredEventsList" :key="event.id">
                        <div class="bg-white dark:bg-[#1c232f] rounded-md overflow-hidden text-center shadow relative">
                            <div class="p-6 pb-0">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="badge" :class="getTypeBadgeClass(event.type)">
                                        {{ getTypeText(event.type) }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ event.user ? event.user.name : $t('events_page.not_available') }}</span>
                                </div>
                                <div class="text-xl font-semibold mb-2">{{ event.title }}</div>
                                <div class="text-white-dark mb-4">{{ event.description || $t('events_page.no_description') }}</div>
                            </div>
                            <div class="px-6 pb-24">
                                <div class="grid grid-cols-1 gap-3 ltr:text-left rtl:text-right">
                                    <div class="flex items-center">
                                        <div class="flex-none ltr:mr-2 rtl:ml-2">
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="text-sm">{{ formatDateTime(event.start) }}</div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="flex-none ltr:mr-2 rtl:ml-2">
                                            <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="text-sm">{{ formatDateTime(event.end) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex gap-4 absolute bottom-0 w-full ltr:left-0 rtl:right-0 p-6">
                                <button 
                                    v-if="authStore.can('edit events') && canEditEvent(event)"
                                    type="button" 
                                    class="btn btn-outline-primary w-1/2" 
                                    @click="editEvent(event)"
                                >
                                    {{ $t('events_page.edit') }}
                                </button>
                                <button 
                                    v-if="authStore.can('delete events') && canEditEvent(event)"
                                    type="button" 
                                    class="btn btn-outline-danger w-1/2" 
                                    @click="deleteEvent(event)"
                                >
                                    {{ $t('events_page.delete') }}
                                </button>
                                <div 
                                    v-if="!canEditEvent(event)"
                                    class="w-full text-center text-xs text-gray-400"
                                >
                                    {{ $t('events_page.read_only') }}
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
        </template>

        <!-- add event modal -->
        <TransitionRoot appear :show="addEventModal" as="template">
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
                            <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-lg text-black dark:text-white-dark">
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
                                    {{ params.id ? $t('events_page.edit_event') : $t('events_page.create_event') }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="saveEvent">
                                        <div class="mb-5">
                                            <label for="title">{{ $t('events_page.title') }} *</label>
                                            <input id="title" type="text" :placeholder="$t('events_page.enter_title')" class="form-input" v-model="params.title" required />
                                            <div v-if="errors.title" class="text-danger mt-1">{{ errors.title[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="description">{{ $t('events_page.description') }}</label>
                                            <textarea id="description" :placeholder="$t('events_page.enter_description')" class="form-input" v-model="params.description" rows="3"></textarea>
                                            <div v-if="errors.description" class="text-danger mt-1">{{ errors.description[0] }}</div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                            <div>
                                                <label for="start">{{ $t('events_page.start_datetime') }} *</label>
                                                <input id="start" type="datetime-local" class="form-input" v-model="params.start" required />
                                                <div v-if="errors.start" class="text-danger mt-1">{{ errors.start[0] }}</div>
                                            </div>
                                            <div>
                                                <label for="end">{{ $t('events_page.end_datetime') }} *</label>
                                                <input id="end" type="datetime-local" class="form-input" v-model="params.end" required />
                                                <div v-if="errors.end" class="text-danger mt-1">{{ errors.end[0] }}</div>
                                            </div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="type">{{ $t('events_page.type') }} *</label>
                                            <select id="type" class="form-select" v-model="params.type" required>
                                                <option value="primary">{{ $t('events_page.type_primary') }}</option>
                                                <option value="info">{{ $t('events_page.type_info') }}</option>
                                                <option value="success">{{ $t('events_page.type_success') }}</option>
                                                <option value="danger">{{ $t('events_page.type_danger') }}</option>
                                            </select>
                                            <div v-if="errors.type" class="text-danger mt-1">{{ errors.type[0] }}</div>
                                        </div>
                                        <div class="flex justify-end items-center mt-8">
                                            <button type="button" class="btn btn-outline-danger" @click="closeModal">{{ $t('events_page.cancel') }}</button>
                                            <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4" :disabled="saving">
                                                <span v-if="saving" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4 ltr:mr-2 rtl:ml-2 inline-block"></span>
                                                {{ params.id ? $t('events_page.update') : $t('events_page.create') }}
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

        <!-- import events modal -->
        <TransitionRoot appear :show="showImportModal" as="template">
            <Dialog as="div" @close="closeImportModal" class="relative z-[51]">
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
                                    @click="closeImportModal"
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
                                    {{ $t('events_page.import_events') }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="importEvents">
                                        <div class="mb-5">
                                            <label for="file">{{ $t('events_page.file') }} *</label>
                                            <input 
                                                id="file" 
                                                type="file" 
                                                class="form-input p-2" 
                                                accept=".csv,.xlsx,.xls"
                                                @change="handleFileUpload"
                                                required 
                                            />
                                            <div class="text-xs text-gray-500 mt-1">{{ $t('events_page.supported_formats') }}</div>
                                            <div v-if="errors.file" class="text-danger mt-1">{{ errors.file[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label class="flex items-center">
                                                <input type="checkbox" class="form-checkbox" v-model="importParams.overwrite" />
                                                <span class="ml-2">{{ $t('events_page.overwrite_existing') }}</span>
                                            </label>
                                            <div class="text-xs text-gray-500 mt-1">{{ $t('events_page.overwrite_description') }}</div>
                                        </div>

                                        <!-- Información de plantilla -->
                                        <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-800 rounded-md">
                                            <h4 class="font-semibold mb-2">{{ $t('events_page.template_info') }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                                {{ $t('events_page.template_instructions') }}
                                            </p>
                                            <div class="flex gap-2">
                                                <button type="button" class="btn btn-outline-primary btn-sm" @click="downloadTemplate('csv')">
                                                    {{ $t('events_page.download_csv') }}
                                                </button>
                                                <button type="button" class="btn btn-outline-info btn-sm" @click="downloadTemplate('xlsx')">
                                                    {{ $t('events_page.download_excel') }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Archivo seleccionado -->
                                        <div v-if="selectedFile" class="mb-5 p-3 bg-gray-100 dark:bg-gray-800 rounded-md">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="20px"
                                                        height="20px"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="ltr:mr-2 rtl:ml-2 text-primary"
                                                    >
                                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                        <polyline points="13 2 13 9 20 9"></polyline>
                                                    </svg>
                                                    <div>
                                                        <div class="font-medium">{{ selectedFile.name }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ (selectedFile.size / 1024).toFixed(2) }} KB
                                                        </div>
                                                    </div>
                                                </div>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger btn-sm"
                                                    @click="removeFile"
                                                >
                                                    {{ $t('events_page.remove') }}
                                                </button>
                                            </div>
                                        </div>

                                        <div class="flex justify-end items-center mt-8">
                                            <button type="button" class="btn btn-outline-danger" @click="closeImportModal">{{ $t('events_page.cancel') }}</button>
                                            <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4" :disabled="importing">
                                                <span v-if="importing" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4 ltr:mr-2 rtl:ml-2 inline-block"></span>
                                                {{ $t('events_page.import') }}
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
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
    import Swal from 'sweetalert2';
    import { useMeta } from '@/composables/use-meta';
    import { useAuthStore } from '@/stores/auth';
    import axios from 'axios';
    import { useI18n } from 'vue-i18n';
    
    const { t } = useI18n();
    useMeta({ title: t('events_page.events_management') });

    const authStore = useAuthStore();

    const displayType = ref('list');
    const addEventModal = ref(false);
    const showImportModal = ref(false);
    const loading = ref(false);
    const saving = ref(false);
    const importing = ref(false);
    const errorMessage = ref('');
    const successMessage = ref('');
    const errors = ref({});
    const searchEvent = ref('');
    
    const defaultParams = ref({
        id: null,
        title: '',
        description: '',
        start: '',
        end: '',
        type: 'primary'
    });
    
    const importParams = ref({
        file: null as File | null,
        overwrite: false
    });
    
    const params = ref({...defaultParams.value});
    const eventsList = ref([]);

    // Computed property for filtered events
    const filteredEventsList = computed(() => {
        if (!searchEvent.value) {
            return eventsList.value;
        }
        const searchTerm = searchEvent.value.toLowerCase();
        return eventsList.value.filter((event) => 
            event.title.toLowerCase().includes(searchTerm) ||
            (event.description && event.description.toLowerCase().includes(searchTerm)) ||
            (event.user && event.user.name.toLowerCase().includes(searchTerm))
        );
    });

    onMounted(() => {
        fetchEvents();
    });

    const fetchEvents = async () => {
        loading.value = true;
        errorMessage.value = '';
        try {
            const response = await axios.get('/api/events');
            eventsList.value = response.data.data;
        } catch (error) {
            console.error('Error fetching events:', error);
            errorMessage.value = t('events_page.error_loading');
            showMessage(t('events_page.error_loading'), 'error');
        } finally {
            loading.value = false;
        }
    };

    const searchEvents = () => {
        // Search is handled by the computed property
    };

    const editEvent = (event: any = null) => {
        errors.value = {};
        if (event) {
            params.value = {
                id: event.id,
                title: event.title,
                description: event.description || '',
                start: formatDateForInput(event.start),
                end: formatDateForInput(event.end),
                type: event.type
            };
        } else {
            params.value = {
                ...defaultParams.value,
                start: new Date().toISOString().slice(0, 16),
                end: new Date(Date.now() + 60 * 60 * 1000).toISOString().slice(0, 16)
            };
        }
        addEventModal.value = true;
    };

    const saveEvent = async () => {
        saving.value = true;
        errors.value = {};
        errorMessage.value = '';
        successMessage.value = '';

        try {
            if (params.value.id) {
                // Update event
                const { data } = await axios.put(`/api/events/${params.value.id}`, params.value);
                const index = eventsList.value.findIndex(event => event.id === params.value.id);
                if (index !== -1) {
                    eventsList.value.splice(index, 1, data);
                }
                showMessage(t('events_page.event_updated'));
            } else {
                // Create event
                const { data } = await axios.post('/api/events', params.value);
                eventsList.value.unshift(data);
                showMessage(t('events_page.event_created'));
            }
            addEventModal.value = false;
        } catch (error: any) {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                errorMessage.value = t('events_page.validation_errors');
            } else {
                console.error('Error saving event:', error);
                errorMessage.value = t('events_page.error_saving');
                showMessage(t('events_page.error_saving'), 'error');
            }
        } finally {
            saving.value = false;
        }
    };

    const deleteEvent = async (event: any) => {
        const result = await Swal.fire({
            title: t('events_page.confirm_delete_title'),
            text: t('events_page.confirm_delete_text'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: t('events_page.yes_delete'),
            cancelButtonText: t('events_page.cancel')
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/events/${event.id}`);
                eventsList.value = eventsList.value.filter(e => e.id !== event.id);
                showMessage(t('events_page.event_deleted'));
            } catch (error: any) {
                console.error('Error deleting event:', error);
                let errorMsg = t('events_page.error_deleting');
                if (error.response?.data?.error) {
                    errorMsg = error.response.data.error;
                }
                showMessage(errorMsg, 'error');
            }
        }
    };

    const handleFileUpload = (event: any) => {
        importParams.value.file = event.target.files[0];
    };

    const importEvents = async () => {
        if (!importParams.value.file) {
            showMessage(t('events_page.no_file_selected'), 'error');
            return;
        }

        importing.value = true;
        errors.value = {};
        errorMessage.value = '';

        const formData = new FormData();
        formData.append('file', importParams.value.file);
        formData.append('overwrite', importParams.value.overwrite.toString());

        try {
            const response = await axios.post('/api/events/import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            if (response.data.success) {
                showMessage(t('events_page.import_success', {
                    imported: response.data.imported,
                    skipped: response.data.skipped
                }));
                closeImportModal();
                fetchEvents(); // Refresh the list
            } else {
                errorMessage.value = t('events_page.import_failed');
                if (response.data.errors) {
                    errors.value = { file: response.data.errors };
                }
            }
        } catch (error: any) {
            console.error('Error importing events:', error);
            if (error.response?.data?.errors) {
                errors.value = { file: error.response.data.errors };
            } else {
                errorMessage.value = t('events_page.import_error');
            }
            showMessage(t('events_page.import_error'), 'error');
        } finally {
            importing.value = false;
        }
    };

    const downloadTemplate = async (format: string = 'xlsx') => {
        try {
            if (format === 'csv') {
                // Crear plantilla CSV directamente en el frontend
                const csvContent = "titulo,inicio,fin,descripcion,tipo\n" +
                    "Reunión de equipo,2025-09-15 09:00:00,2025-09-15 10:30:00,Reunión semanal del equipo de desarrollo,primary\n" +
                    "Viaje de negocios,2025-09-20 08:00:00,2025-09-22 18:00:00,Viaje a conferencia anual,info\n" +
                    "Cita médica,2025-09-25 11:00:00,2025-09-25 12:00:00,Control médico anual,success\n" +
                    "Entrega de proyecto,2025-09-30 17:00:00,2025-09-30 18:00:00,Entrega final del proyecto,danger";
                
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'plantilla_importacion_eventos.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            } else {
                // Descargar plantilla Excel desde el servidor
                const response = await axios.get('/api/events/template', {
                    responseType: 'blob'
                });

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'plantilla_importacion_eventos.xlsx');
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
            }

            showMessage(t('events_page.template_downloaded'));
        } catch (error) {
            console.error('Error downloading template:', error);
            showMessage(t('events_page.template_download_error'), 'error');
        }
    };

    const canEditEvent = (event: any) => {
        // Admin puede editar todos los eventos, usuarios normales solo los suyos
        return authStore.user?.role === 'admin' || event.user_id === authStore.user?.id;
    };

    const getTypeBadgeClass = (type: string) => {
        const classes = {
            primary: 'bg-primary text-white',
            info: 'bg-info text-white',
            success: 'bg-success text-white',
            danger: 'bg-danger text-white'
        };
        return classes[type] || 'bg-secondary text-white';
    };

    const getTypeText = (type: string) => {
        const texts = {
            primary: t('events_page.type_primary'),
            info: t('events_page.type_info'),
            success: t('events_page.type_success'),
            danger: t('events_page.type_danger')
        };
        return texts[type] || type;
    };

    const formatDateTime = (dateString: string) => {
        return new Date(dateString).toLocaleString('es-ES');
    };

    const formatDateForInput = (dateString: string) => {
        const date = new Date(dateString);
        return date.toISOString().slice(0, 16);
    };

    const removeFile = () => {
        importParams.value.file = null;
        const fileInput = document.getElementById('file') as HTMLInputElement;
        if (fileInput) {
            fileInput.value = '';
        }
    };

    const closeModal = () => {
        addEventModal.value = false;
        params.value = {...defaultParams.value};
        errors.value = {};
    };

    const closeImportModal = () => {
        showImportModal.value = false;
        importParams.value = {
            file: null,
            overwrite: false
        };
        errors.value = {};
    };

    const toggleImportModal = () => {
        showImportModal.value = true;
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
</script>