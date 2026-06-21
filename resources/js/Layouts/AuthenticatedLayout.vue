<script setup>
import { ref, onMounted, watch, reactive } from "vue";
import Navigation from "@/Components/App/Navigation.vue";
import SearchForm from "@/Components/App/SearchForm.vue";
import UserSettingsDropDown from "@/Components/App/UserSettingsDropDown.vue";
import {
    FILE_UPLOAD_STARTED,
    emitter,
    showErrorDialog,
    showSuccessNotification,
} from "@/event-bus";
import { usePage, router } from "@inertiajs/vue3";
import axios from "axios";
import FormProgress from "@/Components/App/FormProgress.vue";
import ErrorDialog from "@/Components/ErrorDialog.vue";
import Notification from "@/Components/Notification.vue";
import { Bars3Icon } from "@heroicons/vue/24/outline";

const page = usePage();
const dragOver = ref(false);
const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);

const toggleSidebarCollapse = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value ? '1' : '0');
};

onMounted(() => {
    emitter.on(FILE_UPLOAD_STARTED, uploadFiles);
    // Restore sidebar state from localStorage
    const saved = localStorage.getItem('sidebarCollapsed');
    if (saved === '1') sidebarCollapsed.value = true;
});

watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) {
            return;
        }

        if (flash.message) {
            showSuccessNotification(flash.message);
        }

        if (flash.error) {
            showErrorDialog(flash.error);
        }
    },
    { deep: true }
);

const uploadTasks = ref([]);

const onDrop = (e) => {
    dragOver.value = false;
    const files = e.dataTransfer.files;
    if (!files.length) {
        return;
    }

    uploadFiles(files);
};
const onDragOver = () => {
    dragOver.value = true;
};
const onDragLeave = () => {
    dragOver.value = false;
};

const MAX_CONCURRENT_UPLOADS = 3;

const uploadFiles = (files) => {
    const selectedFiles = Array.from(files);

    if (!selectedFiles.length) {
        showErrorDialog("Please select at least one file to upload.");
        return;
    }

    selectedFiles.forEach((file) => {
        const task = reactive({
            id: Math.random().toString(36).substr(2, 9),
            file: file,
            progress: 0,
            status: 'pending', // pending, uploading, success, error
            error: null
        });
        uploadTasks.value.push(task);
    });

    processUploadQueue();
};

const processUploadQueue = () => {
    const activeUploads = uploadTasks.value.filter(t => t.status === 'uploading').length;
    
    if (activeUploads >= MAX_CONCURRENT_UPLOADS) {
        return;
    }
    
    const pendingTasks = uploadTasks.value.filter(t => t.status === 'pending');
    const tasksToStart = pendingTasks.slice(0, MAX_CONCURRENT_UPLOADS - activeUploads);
    
    tasksToStart.forEach(task => {
        task.status = 'uploading';
        startUploadTask(task);
    });
};

const startUploadTask = async (task) => {
    const formData = new FormData();
    formData.append("parent_id", page.props.rootFolder?.id || '');
    formData.append("files[]", task.file);
    if (task.file.webkitRelativePath) {
        formData.append("relative_paths[]", task.file.webkitRelativePath);
    }

    try {
        await axios.post(route("files.store"), formData, {
            headers: {
                "Accept": "application/json",
                "Content-Type": "multipart/form-data",
            },
            onUploadProgress: (progressEvent) => {
                task.progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            },
        });
        task.status = 'success';
        task.progress = 100;
    } catch (error) {
        task.status = 'error';
        if (error.response?.data?.errors?.['files.0']) {
            task.error = error.response.data.errors['files.0'][0];
        } else if (error.response?.data?.message) {
            task.error = error.response.data.message;
        } else {
            task.error = "Upload failed.";
        }
    } finally {
        processUploadQueue();
        checkAllUploadsCompleted();
    }
};

const checkAllUploadsCompleted = () => {
    const allDone = uploadTasks.value.every(t => t.status === 'success' || t.status === 'error');
    const nonePending = uploadTasks.value.every(t => t.status !== 'pending' && t.status !== 'uploading');
    
    if (allDone && nonePending && uploadTasks.value.length > 0) {
        router.reload({ only: ['files', 'flash'] });
        setTimeout(() => {
            const hasErrors = uploadTasks.value.some(t => t.status === 'error');
            if (!hasErrors) {
                uploadTasks.value = [];
            } else {
                uploadTasks.value = uploadTasks.value.filter(t => t.status === 'error');
            }
        }, 1500);
    }
};

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const closeSidebar = () => {
    sidebarOpen.value = false;
};
</script>

<template>
    <div class="h-screen bg-canvas-soft flex w-full">
        <!-- Mobile Sidebar Overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
            @click="closeSidebar"
        ></div>

        <!-- Sidebar Navigation -->
        <Navigation 
            :sidebar-open="sidebarOpen" 
            :collapsed="sidebarCollapsed"
            @close="closeSidebar"
            @toggle-collapse="toggleSidebarCollapse"
        />

        <main
            class="flex flex-col flex-1 px-2 sm:px-4 overflow-hidden"
            :class="dragOver ? 'dropzone' : ''"
            @drop.prevent="onDrop"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave"
        >
            <template
                v-if="dragOver"
                class="text-ink-mute text-sm text-center py-8"
            >
                Drag files here to upload.
            </template>

            <template v-else>
                <!-- Header with Mobile Menu Button -->
                <div class="relative z-40 flex items-center justify-between w-full gap-2 py-2 sm:py-3 px-1">
                    <!-- Mobile Menu Button -->
                    <button
                        @click="toggleSidebar"
                        class="lg:hidden p-2 rounded-sm hover:bg-canvas transition-colors"
                    >
                        <Bars3Icon class="w-6 h-6 text-ink" />
                    </button>

                    <SearchForm />
                    <UserSettingsDropDown />
                </div>

                <div id="main-scroll-container" class="flex flex-col flex-1 overflow-auto">
                    <div class="flex-1">
                        <slot></slot>
                    </div>
                    
                    <!-- Global Footer -->
                    <footer class="mt-auto py-4 border-t border-hairline">
                        <p class="text-center font-sans text-caption text-ink-mute">
                            Design and Developed by <a href="https://dezignwise.online" target="_blank" class="text-ink border-b border-ink hover:text-primary hover:border-primary transition-colors">DezignWise</a>
                        </p>
                    </footer>
                </div>
            </template>
        </main>
    </div>

    <FormProgress :tasks="uploadTasks" @close="uploadTasks = []" />
    <ErrorDialog />
    <Notification />
</template>

<style scoped>
.dropzone {
    width: 100%;
    height: 100%;
    color: #707070;
    border: 2px dashed #c7c7c7;
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
