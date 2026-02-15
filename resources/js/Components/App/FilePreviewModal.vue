<script setup>
import { ref, watch, computed } from "vue";
import Modal from "@/Components/App/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { XMarkIcon, ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { getPreviewType } from "@/Helper/file-helper.js";

const props = defineProps({
    modelValue: Boolean,
    file: Object,
});

const emit = defineEmits(["update:modelValue", "edit-note"]);

const previewData = ref(null);
const loading = ref(false);
const error = ref(null);

const previewType = computed(() => {
    if (!props.file) return null;
    return getPreviewType(props.file);
});

watch(() => props.modelValue, async (show) => {
    if (show && props.file) {
        await loadPreview();
    } else {
        previewData.value = null;
        error.value = null;
    }
});

const loadPreview = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await fetch(route('files.preview', props.file.id), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Failed to load preview');
        }

        previewData.value = await response.json();
    } catch (e) {
        error.value = 'Unable to load preview';
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const closeModal = () => {
    emit("update:modelValue");
};

const downloadFile = () => {
    if (props.file.type === 'note') {
        window.location.href = route('notes.download', props.file.id);
    } else {
        const urlParams = new URLSearchParams();
        urlParams.append('ids[]', props.file.id);
        window.location.href = `${route('files.download')}?${urlParams.toString()}`;
    }
};

const editNote = () => {
    closeModal();
    emit("edit-note", props.file);
};
</script>

<template>
    <Modal :show="modelValue" @close="closeModal" max-width="6xl">
        <div class="bg-white rounded-lg">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 truncate max-w-md">
                    {{ file?.name }}
                </h3>
                <div class="flex items-center gap-2">
                    <PrimaryButton
                        @click="downloadFile"
                        class="flex items-center gap-2"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        Download
                    </PrimaryButton>
                    <button
                        @click="closeModal"
                        class="text-gray-400 hover:text-gray-600 transition"
                    >
                        <XMarkIcon class="w-6 h-6" />
                    </button>
                </div>
            </div>

            <!-- Preview Content -->
            <div class="p-6">
                <!-- Loading State -->
                <div v-if="loading" class="flex items-center justify-center h-96">
                    <div class="text-gray-500">Loading preview...</div>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="flex items-center justify-center h-96">
                    <div class="text-red-500">{{ error }}</div>
                </div>

                <!-- Image Preview -->
                <div v-else-if="previewType === 'image' && previewData" class="flex items-center justify-center">
                    <img
                        :src="previewData.url"
                        :alt="file.name"
                        class="max-w-full max-h-[70vh] object-contain rounded-lg"
                    />
                </div>

                <!-- PDF Preview -->
                <div v-else-if="previewType === 'pdf' && previewData" class="w-full">
                    <iframe
                        :src="previewData.url"
                        class="w-full h-[70vh] border-0 rounded-lg"
                        title="PDF Preview"
                    ></iframe>
                </div>

                <!-- Video Preview -->
                <div v-else-if="previewType === 'video' && previewData" class="flex items-center justify-center">
                    <video
                        :src="previewData.url"
                        controls
                        class="max-w-full max-h-[70vh] rounded-lg"
                    >
                        Your browser does not support the video tag.
                    </video>
                </div>

                <!-- Note Preview -->
                <div v-else-if="previewType === 'note' && previewData" class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-6 min-h-[50vh] max-h-[70vh] overflow-auto">
                        <pre class="whitespace-pre-wrap font-sans text-gray-800">{{ previewData.content }}</pre>
                    </div>
                    <div class="flex justify-end">
                        <SecondaryButton @click="editNote">
                            Edit Note
                        </SecondaryButton>
                    </div>
                </div>

                <!-- No Preview Available -->
                <div v-else class="flex items-center justify-center h-96">
                    <div class="text-gray-500">Preview not available for this file type</div>
                </div>
            </div>
        </div>
    </Modal>
</template>
