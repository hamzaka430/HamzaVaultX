<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FileIcon from "@/Components/App/FileIcon.vue";
import { ref, onMounted, computed, watch } from "vue";
import { httpGet } from "@/Helper/http-helper";
import Checkbox from "@/Components/Checkbox.vue";
import DownloadFileButton from "@/Components/App/DownloadFileButton.vue";
import EditNoteModal from "@/Components/App/EditNoteModal.vue";
import FilePreviewModal from "@/Components/App/FilePreviewModal.vue";
import { canPreview } from "@/Helper/file-helper.js";


const props = defineProps({
    files: Object,
    folder: Object,
    ancestors: Object,
});

const allFiles = ref({
    data: props.files.data,
    next: props.files.links.next,
});

watch(() => props.files, (newFiles) => {
    allFiles.value = {
        data: newFiles.data,
        next: newFiles.links.next,
    };
});

const allSelected = ref(false);
const selected = ref({});
const editNoteModal = ref(false);
const selectedNote = ref(null);
const previewModal = ref(false);
const previewFile = ref(null);
const scrollContainer = ref(null);

const selectedIds = computed(() => {
    return Object.entries(selected.value)
        .filter((elem) => elem[1])
        .map((elem) => elem[0]);
});

const isLoadingMore = ref(false);

const loadMore = () => {
    if (allFiles.value.next === null || isLoadingMore.value) {
        return;
    }

    isLoadingMore.value = true;
    httpGet(allFiles.value.next).then((res) => {
        allFiles.value.data = [...allFiles.value.data, ...res.data];
        allFiles.value.next = res.links.next;
    }).finally(() => {
        isLoadingMore.value = false;
    });
};

const onSelectAllChange = () => {
    allFiles.value.data.forEach((f) => {
        selected.value[f.id] = allSelected.value;
    });
};

const toggleFileSelect = (file) => {
    selected.value[file.id] = !selected.value[file.id];
    onSelectCheckboxChange(file);
};

const openNote = (file) => {
    if (!file.is_folder && canPreview(file)) {
        previewFile.value = file;
        previewModal.value = true;
    }
};

const handleEditNote = (note) => {
    selectedNote.value = note;
    editNoteModal.value = true;
};

const onSelectCheckboxChange = (file) => {
    if (!selected.value[file.id]) {
        allSelected.value = false;
    } else {
        let checked = true;

        for (let file of allFiles.value.data) {
            if (!selected.value[file.id]) {
                checked = false;
                break;
            }
        }

        allSelected.value = checked;
    }
};

const resetForm = () => {
    allSelected.value = false;
    selected.value = {};
};

const loadMoreIntersect = ref(null);
onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => entry.isIntersecting && loadMore());
        },
        {
            root: scrollContainer.value,
            rootMargin: "0px 0px 1000px 0px",
        }
    );

    observer.observe(loadMoreIntersect.value);
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Shared With Me" />

        <nav class="flex flex-col sm:flex-row sm:items-center justify-between p-2 mb-3 gap-3 sticky top-0 z-30 bg-canvas border-b border-hairline">
            <ol class="inline-flex items-center space-x-1">
                <li class="inline-flex items-center">
                    <Link
                        :href="route('sharedWithMe')"
                        class="flex items-center font-sans text-body-md font-medium text-ink-mute hover:text-ink"
                    >
                        Shared With Me
                    </Link>
                </li>
            </ol>

            <div class="flex flex-wrap items-center gap-4 sm:gap-2">
                <label class="flex md:hidden items-center text-xs sm:text-sm">
                    <Checkbox
                        v-model:checked="allSelected"
                        @change="onSelectAllChange"
                        class="mr-2"
                    />
                    <span class="whitespace-nowrap">Select All</span>
                </label>

                <div class="flex items-center gap-2">
                    <DownloadFileButton
                        :all="allSelected"
                        :ids="selectedIds"
                        :shared-with-me="true"
                    />
                </div>
            </div>
        </nav>

        <div ref="scrollContainer" class="flex-1 overflow-auto">
            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-3 px-1 pt-2 pb-4">
                <div
                    v-for="file in allFiles.data"
                    :key="file.id"
                    class="bg-canvas rounded-lg shadow-elevation-1 p-4 border border-hairline transition-all duration-200"
                    :class="
                        selected[file.id] || allSelected
                            ? 'ring-1 ring-primary bg-canvas-soft'
                            : 'hover:bg-canvas-soft'
                    "
                    @click="($event) => toggleFileSelect(file)"
                >
                    <div class="flex items-start gap-4">
                        <Checkbox
                            v-model="selected[file.id]"
                            :checked="selected[file.id] || allSelected"
                            @change="($event) => onSelectCheckboxChange(file)"
                            @click.stop
                            class="mt-1"
                        />
                        <div class="flex-1 min-w-0" @dblclick="openNote(file)">
                            <div class="flex items-center gap-3 mb-2">
                                <FileIcon :file="file" />
                                <span class="font-sans text-body-md font-medium text-ink truncate">{{ file.name }}</span>
                            </div>
                            <div class="text-xs text-gray-600">
                                <div class="truncate">
                                    <span class="font-medium">Path:</span> {{ file.path }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block">
                <table
                    class="w-full font-sans text-body-md text-left text-ink-mute rounded shadow-elevation-1"
                >
                    <thead
                        class="font-sans text-caption text-ink-mute uppercase tracking-wider bg-canvas border-b border-hairline sticky top-0 z-10 shadow-sm"
                    >
                        <tr>
                            <th class="px-4 lg:px-6 py-3">
                                <Checkbox
                                    v-model:checked="allSelected"
                                    @change="onSelectAllChange"
                                />
                            </th>
                            <th class="px-4 lg:px-6 py-3">Name</th>
                            <th class="px-4 lg:px-6 py-3">Path</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            class="border-b border-hairline hover:bg-canvas-soft cursor-pointer transition ease-in-out duration-200"
                            :class="
                                selected[file.id] || allSelected
                                    ? 'bg-canvas-soft'
                                    : 'bg-canvas'
                            "
                            v-for="file in allFiles.data"
                            :key="file.id"
                            @click="($event) => toggleFileSelect(file)"
                            @dblclick="openNote(file)"
                        >
                            <td
                                class="pl-4 lg:pl-6 py-4 pr-0 font-medium tracking-wider text-ink"
                            >
                                <Checkbox
                                    v-model="selected[file.id]"
                                    :checked="selected[file.id] || allSelected"
                                    @change="
                                        ($event) => onSelectCheckboxChange(file)
                                    "
                                />
                            </td>
                            <td
                                class="px-4 lg:px-6 py-4 font-medium tracking-wider text-ink"
                            >
                                <div class="flex items-center">
                                    <FileIcon :file="file" />
                                    <span class="truncate inline-block max-w-[200px]" :title="file.name">{{ file.name }}</span>
                                </div>
                            </td>
                            <td
                                class="px-4 lg:px-6 py-4 font-medium tracking-wider text-ink"
                            >
                                <span class="truncate block max-w-xs">{{ file.path }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="!allFiles.data.length"
                class="text-center tracking-wide py-3 font-sans text-body-md text-ink-mute bg-canvas shadow-elevation-1 rounded-b-lg border-b border-hairline"
            >
                No files or folders available in this directory.
            </div>

            <div ref="loadMoreIntersect"></div>
        </div>

        <EditNoteModal v-model="editNoteModal" :note="selectedNote" />
        <FilePreviewModal 
            v-model="previewModal" 
            :file="previewFile"
            @edit-note="handleEditNote"
        />
    </AuthenticatedLayout>
</template>
