<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ArrowDownCircleIcon } from "@heroicons/vue/24/outline";
import { computed, ref } from "vue";
import Modal from "@/Components/App/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    all: {
        type: Boolean,
        required: false,
        default: false,
    },
    ids: {
        type: Array,
        required: false,
    },
    sharedWithMe: false,
    sharedByMe: false,
});

const page = usePage();

const btnDisabled = computed(() => {
    return !props.all && !props.ids.length;
});

const showModal = ref(false);

const handleDownloadClick = () => {
    if (!props.all && props.ids.length === 1) {
        // Just one file, download normally without zip prompt
        downloadAsZip();
        return;
    }
    
    if (!props.all && !props.ids.length) {
        return;
    }

    showModal.value = true;
};

const getUrl = () => {
    let url = route("files.download");
    if (props.sharedWithMe) {
        url = route("files.downloadSharedWithMe");
    } else if (props.sharedByMe) {
        url = route("files.downloadSharedByMe");
    }
    return url;
};

const downloadAsZip = () => {
    showModal.value = false;
    const urlParams = new URLSearchParams();
    if (page.props.rootFolder?.id) {
        urlParams.append("parent_id", page.props.rootFolder.id);
    }
    if (props.all) {
        urlParams.append("all", props.all ? 1 : 0);
    } else {
        for (let id of props.ids) {
            urlParams.append("ids[]", id);
        }
    }

    window.location.href = `${getUrl()}?${urlParams.toString()}`;
};

const downloadAsSeparate = () => {
    showModal.value = false;
    const url = getUrl();
    
    // For each ID, trigger a download using an iframe
    for (let id of props.ids) {
        const urlParams = new URLSearchParams();
        if (page.props.rootFolder?.id) {
            urlParams.append("parent_id", page.props.rootFolder.id);
        }
        urlParams.append("ids[]", id);
        
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = `${url}?${urlParams.toString()}`;
        document.body.appendChild(iframe);
    }
};
</script>

<template>
    <button
        class="inline-flex items-center px-4 py-2 font-sans text-body-md font-medium text-ink bg-canvas border border-hairline rounded-lg hover:bg-canvas-soft focus:z-10 focus:ring-2 focus:ring-primary transition-colors"
        @click="handleDownloadClick"
        :disabled="btnDisabled"
        :class="btnDisabled ? 'opacity-50 cursor-not-allowed' : ''"
    >
        <ArrowDownCircleIcon class="w-4 h-4 mr-2" /> Download
    </button>

    <Modal :show="showModal" @close="showModal = false">
        <div class="p-6 bg-canvas text-ink">
            <h2 class="text-lg font-medium font-sans">
                Download Multiple Files
            </h2>
            <p class="mt-1 font-sans text-body-md text-ink-mute">
                Would you like to download these files packaged together as a single ZIP archive, or download each file separately?
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <SecondaryButton @click="showModal = false">
                    Cancel
                </SecondaryButton>
                <SecondaryButton @click="downloadAsSeparate">
                    Separate Files
                </SecondaryButton>
                <PrimaryButton @click="downloadAsZip">
                    ZIP Archive
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
