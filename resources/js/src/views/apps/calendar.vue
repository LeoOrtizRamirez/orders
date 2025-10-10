<template>
    <div>
        <div class="panel">
            <div class="mb-5">
                <div class="mb-4 flex items-center sm:flex-row flex-col sm:justify-between justify-center">
                    <div class="sm:mb-0 mb-4">
                        <div class="text-lg font-semibold ltr:sm:text-left rtl:sm:text-right text-center">{{ $t('calendar_page.calendar') }}</div>
                        <div class="flex items-center mt-2 flex-wrap sm:justify-start justify-center">
                            <div class="flex items-center ltr:mr-4 rtl:ml-4">
                                <div class="h-2.5 w-2.5 rounded-sm ltr:mr-2 rtl:ml-2 bg-primary"></div>
                                <div>{{ $t('calendar_page.work') }}</div>
                            </div>
                            <div class="flex items-center ltr:mr-4 rtl:ml-4">
                                <div class="h-2.5 w-2.5 rounded-sm ltr:mr-2 rtl:ml-2 bg-info"></div>
                                <div>{{ $t('calendar_page.travel') }}</div>
                            </div>
                            <div class="flex items-center ltr:mr-4 rtl:ml-4">
                                <div class="h-2.5 w-2.5 rounded-sm ltr:mr-2 rtl:ml-2 bg-success"></div>
                                <div>{{ $t('calendar_page.personal') }}</div>
                            </div>
                            <div class="flex items-center">
                                <div class="h-2.5 w-2.5 rounded-sm ltr:mr-2 rtl:ml-2 bg-danger"></div>
                                <div>{{ $t('calendar_page.important') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Botón de importar eventos -->
                        <button type="button" class="btn btn-outline-primary" @click="toggleImportModal">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="16px"
                                height="16px"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="w-4 h-4 ltr:mr-1 rtl:ml-1"
                            >
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            {{ $t('calendar_page.import_events') }}
                        </button>
                        <!-- Botón de crear evento existente -->
                        <button type="button" class="btn btn-primary" @click="editEvent()">
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
                                class="w-5 h-5 ltr:mr-2 rtl:ml-2"
                            >
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            {{ $t('calendar_page.create_event') }}
                        </button>
                    </div>
                </div>
                <div class="calendar-wrapper">
                    <FullCalendar ref="calendar" :options="calendarOptions">
                        <template v-slot:eventContent="arg">
                            <div class="fc-event-main-frame flex items-center px-1 py-0.5 text-white">
                                <div class="fc-event-time font-semibold px-0.5">
                                    {{ arg.timeText }}
                                </div>
                                <div class="fc-event-title-container">
                                    <div class="fc-event-title fc-sticky !font-medium px-0.5">
                                        {{ arg.event.title }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </FullCalendar>
                </div>
            </div>
        </div>
        <!-- add event modal -->
        <TransitionRoot appear :show="isAddEventModal" as="template">
            <Dialog as="div" @close="isAddEventModal = false" class="relative z-[51]">
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
                                    @click="isAddEventModal = false"
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
                                    {{ params.id ? $t('calendar_page.edit_event') : $t('calendar_page.create_event') }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="saveEvent">
                                        <div class="mb-5">
                                            <label for="title">{{ $t('calendar_page.event_title') }}:</label>
                                            <input
                                                id="title"
                                                type="text"
                                                name="title"
                                                class="form-input"
                                                :placeholder="$t('calendar_page.event_title')"
                                                v-model="params.title"
                                                required
                                            />
                                            <div class="text-danger mt-2" id="titleErr"></div>
                                        </div>

                                        <div class="mb-5">
                                            <label for="dateStart">{{ $t('calendar_page.from') }}:</label>
                                            <input
                                                id="dateStart"
                                                type="datetime-local"
                                                name="start"
                                                class="form-input"
                                                :placeholder="$t('calendar_page.from')"
                                                v-model="params.start"
                                                :min="minStartDate"
                                                @change="startDateChange($event)"
                                                required
                                            />
                                            <div class="text-danger mt-2" id="startDateErr"></div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="dateEnd">{{ $t('calendar_page.to') }}:</label>
                                            <input
                                                id="dateEnd"
                                                type="datetime-local"
                                                name="end"
                                                class="form-input"
                                                :placeholder="$t('calendar_page.to')"
                                                v-model="params.end"
                                                :min="minEndDate"
                                                required
                                            />
                                            <div class="text-danger mt-2" id="endDateErr"></div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="description">{{ $t('calendar_page.event_description') }}:</label>
                                            <textarea
                                                id="description"
                                                name="description"
                                                class="form-textarea min-h-[130px]"
                                                :placeholder="$t('calendar_page.event_description')"
                                                v-model="params.description"
                                            ></textarea>
                                        </div>
                                        <div class="mb-5">
                                            <label>{{ $t('calendar_page.badge') }}:</label>
                                            <div class="mt-3">
                                                <label class="inline-flex cursor-pointer ltr:mr-3 rtl:ml-3">
                                                    <input type="radio" class="form-radio" name="badge" value="primary" v-model="params.type" />
                                                    <span class="ltr:pl-2 rtl:pr-2">{{ $t('calendar_page.work') }}</span>
                                                </label>
                                                <label class="inline-flex cursor-pointer ltr:mr-3 rtl:ml-3">
                                                    <input type="radio" class="form-radio text-info" name="badge" value="info" v-model="params.type" />
                                                    <span class="ltr:pl-2 rtl:pr-2">{{ $t('calendar_page.travel') }}</span>
                                                </label>
                                                <label class="inline-flex cursor-pointer ltr:mr-3 rtl:ml-3">
                                                    <input type="radio" class="form-radio text-success" name="badge" value="success" v-model="params.type" />
                                                    <span class="ltr:pl-2 rtl:pr-2">{{ $t('calendar_page.personal') }}</span>
                                                </label>
                                                <label class="inline-flex cursor-pointer">
                                                    <input type="radio" class="form-radio text-danger" name="badge" value="danger" v-model="params.type" />
                                                    <span class="ltr:pl-2 rtl:pr-2">{{ $t('calendar_page.important') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="flex justify-end items-center mt-8 gap-2">
                                            <!-- Botón de eliminar (solo visible cuando se edita) -->
                                            <button 
                                                v-if="params.id" 
                                                type="button" 
                                                class="btn btn-outline-danger" 
                                                @click="confirmDeleteEvent"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16px"
                                                    height="16px"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="w-4 h-4 ltr:mr-1 rtl:ml-1"
                                                >
                                                    <path d="M3 6h18"></path>
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                                </svg>
                                                {{ $t('calendar_page.delete_event') }}
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" @click="isAddEventModal = false">
                                                {{ $t('calendar_page.cancel') }}
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                {{ params.id ? $t('calendar_page.update_event') : $t('calendar_page.create_event') }}
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

        <!-- Modal de importación de eventos -->
        <TransitionRoot appear :show="isImportModal" as="template">
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
                                    {{ $t('calendar_page.import_events') }}
                                </div>
                                <div class="p-5">
                                    <div class="mb-5">
                                        <p class="mb-4">{{ $t('calendar_page.import_instructions') }}</p>
                                        
                                        <!-- Enlaces de descarga de plantillas -->
                                        <div class="mb-4 flex justify-center gap-4">
                                            <a 
                                                href="#" 
                                                @click.prevent="downloadTemplate('csv')" 
                                                class="text-primary hover:underline flex items-center"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16px"
                                                    height="16px"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="w-4 h-4 ltr:mr-1 rtl:ml-1"
                                                >
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                                {{ $t('calendar_page.download_csv_template') }}
                                            </a>
                                            <a 
                                                href="#" 
                                                @click.prevent="downloadTemplate('xlsx')" 
                                                class="text-primary hover:underline flex items-center"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16px"
                                                    height="16px"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="w-4 h-4 ltr:mr-1 rtl:ml-1"
                                                >
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                                {{ $t('calendar_page.download_excel_template') }}
                                            </a>
                                        </div>
                                        
                                        <div class="border border-dashed border-[#e0e6ed] dark:border-[#1b2e4b] rounded-md p-5">
                                            <div class="flex justify-center items-center">
                                                <label
                                                    for="importFile"
                                                    class="w-full cursor-pointer"
                                                >
                                                    <input
                                                        id="importFile"
                                                        type="file"
                                                        ref="fileInput"
                                                        accept=".csv,.xlsx,.xls"
                                                        class="hidden"
                                                        @change="handleFileUpload"
                                                    />
                                                    <div class="mx-auto text-center">
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="32px"
                                                            height="32px"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="1.5"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="w-12 h-12 mx-auto text-primary mb-2"
                                                        >
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                            <polyline points="17 8 12 3 7 8"></polyline>
                                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                                        </svg>
                                                        <h4 class="text-lg font-semibold">
                                                            {{ $t('calendar_page.drop_file_here') }}
                                                        </h4>
                                                        <p class="text-gray-500 dark:text-gray-400">
                                                            {{ $t('calendar_page.supported_formats') }}
                                                        </p>
                                                        <button
                                                            type="button"
                                                            class="btn btn-primary mt-4"
                                                            @click="triggerFileInput"
                                                        >
                                                            {{ $t('calendar_page.select_file') }}
                                                        </button>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div v-if="selectedFile" class="mt-4 p-3 bg-gray-100 dark:bg-gray-800 rounded-md">
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
                                                    <span>{{ selectedFile.name }}</span>
                                                </div>
                                                <button
                                                    type="button"
                                                    class="text-danger hover:text-red-700"
                                                    @click="removeFile"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16px"
                                                        height="16px"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    >
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-5">
                                        <label class="flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                class="form-checkbox"
                                                v-model="importOptions.overwrite"
                                            />
                                            <span class="ltr:ml-2 rtl:mr-2">{{ $t('calendar_page.overwrite_existing') }}</span>
                                        </label>
                                    </div>
                                    <div class="flex justify-end items-center mt-8 gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary"
                                            @click="closeImportModal"
                                        >
                                            {{ $t('calendar_page.cancel') }}
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-primary"
                                            :disabled="!selectedFile"
                                            @click="importEvents"
                                        >
                                            {{ $t('calendar_page.import') }}
                                        </button>
                                    </div>
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
    import { computed, onMounted, ref } from 'vue';
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
    import FullCalendar from '@fullcalendar/vue3';
    import dayGridPlugin from '@fullcalendar/daygrid';
    import timeGridPlugin from '@fullcalendar/timegrid';
    import interactionPlugin from '@fullcalendar/interaction';
    import Swal from 'sweetalert2';
    import { useMeta } from '@/composables/use-meta';
    import axios from 'axios';
    import { useI18n } from 'vue-i18n';
    import * as XLSX from 'xlsx';

    const { t } = useI18n();
    useMeta({ title: t('calendar_page.calendar') });

    const defaultParams = ref({
        id: null,
        title: '',
        start: '',
        end: '',
        description: '',
        type: 'primary',
    });
    const params = ref({
        id: null,
        title: '',
        start: '',
        end: '',
        description: '',
        type: 'primary',
    });
    const isAddEventModal = ref(false);
    const minStartDate: any = ref('');
    const minEndDate: any = ref('');
    const calendar: any = ref(null);
    const events: any = ref([]);
    
    // Variables para la importación
    const isImportModal = ref(false);
    const selectedFile = ref<File | null>(null);
    const fileInput = ref<HTMLInputElement | null>(null);
    const importOptions = ref({
        overwrite: false,
    });

    const calendarOptions = computed(() => {
        return {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },
            editable: true,
            dayMaxEvents: true,
            selectable: true,
            droppable: true,
            eventClick: (event: any) => {
                editEvent(event);
            },
            select: (event: any) => {
                editDate(event);
            },
            events: events.value,
            locale: 'es',
            buttonText: {
                today: t('calendar_page.today'),
                month: t('calendar_page.month_view'),
                week: t('calendar_page.week_view'),
                day: t('calendar_page.day_view'),
            },
        };
    });

    onMounted(() => {
        getEvents();
    });

    const getEvents = async () => {
        try {
            const response = await axios.get('/api/events');
            events.value = response.data.data.map((event: any) => ({
                id: event.id,
                title: event.title,
                start: event.start,
                end: event.end,
                description: event.description,
                className: event.type,
            }));
        } catch (error) {
            console.error('Error fetching events:', error);
            showMessage(t('calendar_page.error_loading'), 'error');
        }
    };

    const editEvent = async (data: any = null) => {
        params.value = JSON.parse(JSON.stringify(defaultParams.value));
        if (data) {
            let obj = JSON.parse(JSON.stringify(data.event));
            params.value = {
                id: obj.id ? obj.id : null,
                title: obj.title ? obj.title : null,
                start: dateFormat(obj.start),
                end: dateFormat(obj.end),
                type: obj.classNames ? obj.classNames[0] : 'primary',
                description: obj.extendedProps ? obj.extendedProps.description : '',
            };
            minStartDate.value = new Date();
            minEndDate.value = dateFormat(obj.start);
        } else {
            minStartDate.value = new Date();
            minEndDate.value = new Date();
        }

        isAddEventModal.value = true;
    };

    const editDate = (data: any) => {
        let obj = {
            event: {
                start: data.start,
                end: data.end,
            },
        };
        editEvent(obj);
    };

    const dateFormat = (dt: any) => {
        dt = new Date(dt);
        const month = dt.getMonth() + 1 < 10 ? '0' + (dt.getMonth() + 1) : dt.getMonth() + 1;
        const date = dt.getDate() < 10 ? '0' + dt.getDate() : dt.getDate();
        const hours = dt.getHours() < 10 ? '0' + dt.getHours() : dt.getHours();
        const mins = dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes();
        dt = dt.getFullYear() + '-' + month + '-' + date + 'T' + hours + ':' + mins;
        return dt;
    };

    const saveEvent = async () => {
        if (!params.value.title) {
            showMessage(t('calendar_page.title_required'), 'error');
            return;
        }
        if (!params.value.start) {
            showMessage(t('calendar_page.start_date_required'), 'error');
            return;
        }
        if (!params.value.end) {
            showMessage(t('calendar_page.end_date_required'), 'error');
            return;
        }

        try {
            if (params.value.id) {
                // Update event
                await axios.put(`/api/events/${params.value.id}`, params.value);
                showMessage(t('calendar_page.event_updated'));
            } else {
                // Create event
                await axios.post('/api/events', params.value);
                showMessage(t('calendar_page.event_saved'));
            }
            
            // Refresh events
            await getEvents();
            isAddEventModal.value = false;
        } catch (error) {
            console.error('Error saving event:', error);
            showMessage(t('calendar_page.error_saving'), 'error');
        }
    };

    // Función para confirmar la eliminación de evento
    const confirmDeleteEvent = async () => {
        const result = await Swal.fire({
            title: t('calendar_page.confirm_delete_title'),
            text: t('calendar_page.confirm_delete_text'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: t('calendar_page.delete_confirm'),
            cancelButtonText: t('calendar_page.cancel')
        });

        if (result.isConfirmed) {
            await deleteEvent();
        }
    };

    // Función para eliminar evento
    const deleteEvent = async () => {
        try {
            if (!params.value.id) {
                showMessage(t('calendar_page.error_deleting'), 'error');
                return;
            }

            await axios.delete(`/api/events/${params.value.id}`);
            showMessage(t('calendar_page.event_deleted'));
            
            // Refresh events
            await getEvents();
            isAddEventModal.value = false;
        } catch (error) {
            console.error('Error deleting event:', error);
            showMessage(t('calendar_page.error_deleting'), 'error');
        }
    };

    const startDateChange = (event: any) => {
        const dateStr = event.target.value;
        if (dateStr) {
            minEndDate.value = dateFormat(dateStr);
            params.value.end = '';
        }
    };

    // Función para abrir el modal de importación
    const toggleImportModal = () => {
        isImportModal.value = !isImportModal.value;
        if (isImportModal.value) {
            selectedFile.value = null;
            importOptions.value.overwrite = false;
        }
    };

    // Función para cerrar el modal de importación
    const closeImportModal = () => {
        isImportModal.value = false;
        selectedFile.value = null;
    };

    // Disparar el input de archivo
    const triggerFileInput = () => {
        if (fileInput.value) {
            fileInput.value.click();
        }
    };

    // Manejar la subida de archivos
    const handleFileUpload = (event: Event) => {
        const target = event.target as HTMLInputElement;
        if (target.files && target.files.length > 0) {
            selectedFile.value = target.files[0];
        }
    };

    // Remover archivo seleccionado
    const removeFile = () => {
        selectedFile.value = null;
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    };

    // Descargar plantilla de importación
    const downloadTemplate = async (format: string) => {
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
                
                if (link.parentNode) {
                    link.parentNode.removeChild(link);
                }
                window.URL.revokeObjectURL(url);
            }
            
            showMessage(t('calendar_page.template_downloaded'));
        } catch (error) {
            console.error('Error downloading template:', error);
            showMessage(t('calendar_page.template_download_error'), 'error');
        }
    };

    // Importar eventos desde el archivo
    const importEvents = async () => {
        if (!selectedFile.value) {
            showMessage(t('calendar_page.no_file_selected'), 'error');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('file', selectedFile.value);
            formData.append('overwrite', importOptions.value.overwrite.toString());

            const response = await axios.post('/api/events/import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });

            if (response.data.success) {
                showMessage(
                    `${t('calendar_page.import_success')} ${response.data.imported} ${t('calendar_page.events_imported')}. 
                     ${response.data.skipped > 0 ? `${response.data.skipped} ${t('calendar_page.events_skipped')}.` : ''}`
                );
                
                // Recargar eventos
                await getEvents();
                closeImportModal();
            } else {
                showMessage(t('calendar_page.import_failed'), 'error');
            }
        } catch (error: any) {
            console.error('Error importing events:', error);
            if (error.response && error.response.data && error.response.data.errors) {
                const errorMessages = error.response.data.errors.join(', ');
                showMessage(`${t('calendar_page.import_error')}: ${errorMessages}`, 'error');
            } else {
                showMessage(t('calendar_page.import_error_general'), 'error');
            }
        }
    };

    const showMessage = (msg = '', type = 'success') => {
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