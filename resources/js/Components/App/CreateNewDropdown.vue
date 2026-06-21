<script setup>
import { ref, computed } from "vue";
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import CreateFolderModal from "@/Components/App/CreateFolderModal.vue";
import CreateNoteModal from "@/Components/App/CreateNoteModal.vue";
import FileUploadMenuItem from "@/Components/App/FileUploadMenuItem.vue";
import FolderUploadMenuItem from "@/Components/App/FolderUploadMenuItem.vue";
import { usePage } from "@inertiajs/vue3";
import { PlusIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    collapsed: { type: Boolean, default: false },
});

const createFolderModal = ref(false);
const createNoteModal = ref(false);

const showCreateFolderModal = () => {
    createFolderModal.value = true;
};

const showCreateNoteModal = () => {
    createNoteModal.value = true;
};

const btnDisabled = computed(() => {
    return usePage().props.current_route !== "myFiles";
});
</script>

<template>
    <Menu as="div" class="relative block text-left">
        <MenuButton
            :class="[
                'flex w-full justify-center gap-x-1.5 rounded-sm bg-primary font-sans text-button-md text-on-primary shadow-sm hover:bg-primary-deep active:bg-primary-deep transition-all duration-300',
                props.collapsed ? 'p-2.5' : 'px-3 py-2',
                btnDisabled ? 'disabled:opacity-50 cursor-not-allowed' : ''
            ]"
            :disabled="btnDisabled"
        >
            <PlusIcon v-if="props.collapsed" class="w-5 h-5 text-gray-600" />
            <span v-else>Create New</span>
        </MenuButton>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                :class="[
                    'absolute w-56 divide-y divide-hairline rounded-sm bg-canvas shadow-elevation-2 ring-1 ring-hairline focus:outline-none z-[9999]',
                    props.collapsed ? 'left-full top-0 ml-2' : 'left-0 mt-2 origin-top-left'
                ]"
            >
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <a
                            href="#"
                            :class="['block px-4 py-2 font-sans text-body-md transition-colors', active ? 'bg-canvas-soft text-ink' : 'text-ink-mute']"
                            @click.prevent="showCreateFolderModal"
                            >New Folder</a
                        >
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <a
                            href="#"
                            :class="['block px-4 py-2 font-sans text-body-md transition-colors', active ? 'bg-canvas-soft text-ink' : 'text-ink-mute']"
                            @click.prevent="showCreateNoteModal"
                            >New Note</a
                        >
                    </MenuItem>
                </div>
                <div class="px-1 py-1">
                    <FileUploadMenuItem />

                    <FolderUploadMenuItem />
                </div>
            </MenuItems>
        </transition>
    </Menu>

    <CreateFolderModal v-model="createFolderModal" />
    <CreateNoteModal v-model="createNoteModal" />
</template>
