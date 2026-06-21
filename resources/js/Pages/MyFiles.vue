<script setup>
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    ChevronRightIcon,
    HomeIcon,
    StarIcon as StarSolidIcon,
} from "@heroicons/vue/20/solid";
import FileIcon from "@/Components/App/FileIcon.vue";
import { ref, onMounted, computed, watch, nextTick } from "vue";
import { httpGet, httpPost } from "@/Helper/http-helper";
import Checkbox from "@/Components/Checkbox.vue";
import DeleteFileButton from "@/Components/App/DeleteFileButton.vue";
import DownloadFileButton from "@/Components/App/DownloadFileButton.vue";
import { StarIcon as StarOutlineIcon } from "@heroicons/vue/24/outline";
import { ON_SEARCH, emitter, showSuccessNotification } from "@/event-bus";
import ShareFileButton from "@/Components/App/ShareFileButton.vue";
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
const onlyFavourites = ref(false);
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

const openFolder = (file) => {
    // If file can be previewed, open preview modal
    if (!file.is_folder && canPreview(file)) {
        previewFile.value = file;
        previewModal.value = true;
        return;
    }
    
    if (!file.is_folder) {
        return;
    }

    router.visit(route("myFiles", { folder: file.path }));
};

const handleEditNote = (note) => {
    selectedNote.value = note;
    editNoteModal.value = true;
};

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

const onDelete = () => {
    allSelected.value = false;
    selected.value = {};
};

const toggleFavourite = (file) => {
    let actionType = "favourited";
    if (file.is_favourite) {
        actionType = "unfavourited";
    }

    httpPost(route("files.toggleFavourite"), { id: file.id }).then(() => {
        file.is_favourite = !file.is_favourite;
        // Trigger reactivity by updating the files array
        const index = allFiles.value.data.findIndex(f => f.id === file.id);
        if (index !== -1) {
            allFiles.value.data[index] = { ...file };
        }
        showSuccessNotification(
            `The file has been successfully ${actionType}.`
        );
    });
};

const showOnlyFavourites = () => {
    const favourites = usePage().props.favourites;

    if (favourites === true) {
        return router.get(route("myFiles"));
    }

    return router.get(route("myFiles"), { favourites: 1 });
};

const loadMoreIntersect = ref(null);
const page = usePage();
let search = ref("");
onMounted(() => {
    const favourites = page.props.favourites;
    onlyFavourites.value = favourites === true;
    search.value = page.props.search ?? "";
    emitter.on(ON_SEARCH, (value) => {
        search.value = value;
    });

    nextTick(() => {
        const rootElement = document.getElementById('main-scroll-container');
        if (rootElement) {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => entry.isIntersecting && loadMore());
                },
                {
                    root: rootElement,
                    rootMargin: "0px 0px 1000px 0px",
                }
            );

            if (loadMoreIntersect.value) {
                observer.observe(loadMoreIntersect.value);
            }
        }
    });
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="My Files" />

        <div class="sticky top-0 z-30 bg-canvas shadow-elevation-1">
            <!-- Breadcrumbs + Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-2 gap-3 bg-canvas border-b border-hairline">
                <ol class="inline-flex items-center space-x-1 overflow-x-auto flex-nowrap">
                    <li
                        v-for="ancestor in ancestors.data"
                        :key="ancestor.id"
                        class="inline-flex items-center flex-shrink-0"
                    >
                        <Link
                            v-if="!ancestor.parent_id"
                            :href="route('myFiles')"
                            class="flex items-center font-sans text-body-md font-medium text-ink-mute hover:text-ink"
                        >
                            <HomeIcon class="w-4 h-4 mr-1" />
                            My Files
                        </Link>

                        <div v-else class="flex items-center">
                            <ChevronRightIcon class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" />
                            <Link
                                :href="route('myFiles', { folder: ancestor.path })"
                                class="font-sans text-body-md font-medium text-ink-mute hover:text-ink truncate max-w-[150px] sm:max-w-none"
                            >
                                {{ ancestor.name }}
                            </Link>
                        </div>
                    </li>
                </ol>

                <div class="flex flex-wrap items-center gap-4 sm:gap-2">
                    <div class="flex items-center gap-4">
                        <label class="flex md:hidden items-center text-xs sm:text-sm">
                            <Checkbox
                                v-model:checked="allSelected"
                                @change="onSelectAllChange"
                                class="mr-2"
                            />
                            <span class="whitespace-nowrap">Select All</span>
                        </label>

                        <label class="flex items-center text-xs sm:text-sm">
                            <Checkbox
                                v-model:checked="onlyFavourites"
                                @change="showOnlyFavourites"
                                class="mr-2"
                            />
                            <span class="whitespace-nowrap">Only Favorites</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-2">
                        <ShareFileButton
                            :all-selected="allSelected"
                            :selected-ids="selectedIds"
                        />

                        <DownloadFileButton
                            :all="allSelected"
                            :ids="selectedIds"
                        />

                        <DeleteFileButton
                            :delete-all="allSelected"
                            :delete-ids="selectedIds"
                            @delete="onDelete"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div ref="scrollContainer" class="flex-1">
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
                        <div class="flex-1 min-w-0" @dblclick="openFolder(file)">
                            <div class="flex items-center gap-3 mb-2">
                                <FileIcon :file="file" />
                                <span class="font-sans text-body-md font-medium text-ink truncate">{{ file.name }}</span>
                                <button
                                    @click.stop.prevent="toggleFavourite(file)"
                                    class="flex-shrink-0"
                                >
                                    <StarOutlineIcon
                                        v-if="!file.is_favourite"
                                        class="w-5 h-5 text-gray-400 hover:text-yellow-500"
                                    />
                                    <StarSolidIcon
                                        v-else
                                        class="w-5 h-5 text-yellow-500"
                                    />
                                </button>
                            </div>
                            <div class="text-xs text-gray-600 space-y-1">
                                <div v-if="search" class="truncate">
                                    <span class="font-medium">Path:</span> {{ file.path }}
                                </div>
                                <div class="flex items-center justify-between">
                                    <span><span class="font-medium">Owner:</span> {{ file.owner }}</span>
                                    <span><span class="font-medium">Size:</span> {{ file.size }}</span>
                                </div>
                                <div class="text-gray-500">
                                    {{ file.updated_at }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block">
                <table class="w-full font-sans text-body-md text-left text-ink-mute border-collapse">
                    <thead class="font-sans text-caption text-ink-mute uppercase tracking-wider bg-canvas border-b border-hairline sticky top-[53px] z-20 shadow-sm">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 font-semibold w-12 text-center">
                                <Checkbox v-model:checked="allSelected" @change="onSelectAllChange" />
                            </th>
                            <th class="px-2 w-10"></th>
                            <th class="px-4 lg:px-6 py-3 font-semibold text-left">Name</th>
                            <th class="px-4 lg:px-6 py-3 font-semibold text-left" v-if="search">Path</th>
                            <th class="px-4 lg:px-6 py-3 font-semibold text-left">Owner</th>
                            <th class="px-4 lg:px-6 py-3 font-semibold text-left">Size</th>
                            <th class="px-4 lg:px-6 py-3 font-semibold text-left">Last Modified</th>
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
                            @dblclick="openFolder(file)"
                            @click="($event) => toggleFileSelect(file)"
                        >
                            <td class="pl-4 lg:pl-6 py-4 pr-0 font-medium tracking-wider text-ink">
                                <Checkbox
                                    v-model="selected[file.id]"
                                    :checked="selected[file.id] || allSelected"
                                    @change="($event) => onSelectCheckboxChange(file)"
                                />
                            </td>
                            <td class="py-4 font-medium tracking-wider text-ink">
                                <div
                                    class="flex items-center"
                                    @click.stop.prevent="toggleFavourite(file)"
                                >
                                    <StarOutlineIcon v-if="!file.is_favourite" class="w-4 h-4" />
                                    <StarSolidIcon v-else class="w-4 h-4 text-yellow-500" />
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4 font-medium tracking-wider text-gray-900">
                                <div class="flex items-center">
                                    <FileIcon :file="file" />
                                    <span class="truncate inline-block max-w-[200px]" :title="file.name">{{ file.name }}</span>
                                </div>
                            </td>
                            <td
                                v-if="search"
                                class="px-4 lg:px-6 py-4 font-medium tracking-wider text-ink"
                            >
                                <span class="truncate block max-w-xs">{{ file.path }}</span>
                            </td>
                            <td class="px-4 lg:px-6 py-4 font-medium tracking-wider text-gray-900 whitespace-nowrap">
                                {{ file.owner }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 font-medium tracking-wider text-gray-900 whitespace-nowrap">
                                {{ file.size }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 font-medium tracking-wider text-gray-900 whitespace-nowrap">
                                {{ file.updated_at }}
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

        </div><!-- end scrollContainer -->

        <EditNoteModal v-model="editNoteModal" :note="selectedNote" />
        <FilePreviewModal 
            v-model="previewModal" 
            :file="previewFile"
            @edit-note="handleEditNote"
        />
    </AuthenticatedLayout>
</template>
