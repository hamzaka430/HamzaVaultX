<script setup>
import { Link } from "@inertiajs/vue3";
import CreateNewDropdown from "@/Components/App/CreateNewDropdown.vue";
import { XMarkIcon, ChevronLeftIcon, ChevronRightIcon as ChevronRightSideIcon, CloudIcon } from "@heroicons/vue/24/outline";
import { FolderIcon, UsersIcon, ShareIcon, TrashIcon } from "@heroicons/vue/24/outline";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
    sidebarOpen: {
        type: Boolean,
        default: false,
    },
    collapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close", "toggle-collapse"]);

const handleNavClick = () => {
    if (window.innerWidth < 1024) {
        emit("close");
    }
};
</script>

<template>
    <!-- Desktop Sidebar -->
    <nav
        :class="[
            'hidden lg:flex lg:flex-col bg-canvas border-r border-hairline transition-all duration-300 ease-in-out flex-shrink-0 relative',
            collapsed ? 'w-[68px]' : 'w-60 xl:w-64'
        ]"
    >
        <!-- Logo Section -->
        <div class="flex items-center justify-center py-12 border-b border-hairline overflow-hidden">
            <Link :href="route('myFiles')" class="flex items-center justify-center">
                <CloudIcon v-if="collapsed" class="w-8 h-8 text-ink transition-all duration-300" />
                <ApplicationLogo v-else class="h-14 w-auto transition-all duration-300" />
            </Link>
        </div>

        <!-- Navigation Content -->
        <div class="flex-1 flex flex-col">
            <!-- Create New Button - kept outside overflow-x-hidden so dropdown is not clipped -->
            <div :class="['py-4 transition-all duration-300', collapsed ? 'px-2' : 'px-4']">
                <CreateNewDropdown :collapsed="collapsed" />
            </div>

            <!-- Nav Links - overflow-x-hidden only here to clip text during animation -->
            <div class="overflow-x-hidden flex-1">
            <nav class="flex-1 px-2 py-2 space-y-1">
                <Link
                    :href="route('myFiles')"
                    :class="[
                        'flex items-center rounded-sm transition-all duration-200 group',
                        collapsed ? 'justify-center px-0 py-2.5' : 'px-3 py-2.5',
                        $page.props.current_route === 'myFiles'
                            ? 'bg-canvas-soft text-ink font-medium'
                            : 'text-ink-mute hover:bg-canvas-soft hover:text-ink'
                    ]"
                    :title="collapsed ? 'My Files' : ''"
                    @click="handleNavClick"
                >
                    <FolderIcon :class="['flex-shrink-0 transition-all duration-300', collapsed ? 'w-6 h-6' : 'w-5 h-5 mr-3', $page.props.current_route === 'myFiles' ? 'text-ink' : 'text-ink-mute group-hover:text-ink']" />
                    <span v-if="!collapsed" class="font-sans text-body-md whitespace-nowrap">My Files</span>
                </Link>

                <Link
                    :href="route('sharedWithMe')"
                    :class="[
                        'flex items-center rounded-sm transition-all duration-200 group',
                        collapsed ? 'justify-center px-0 py-2.5' : 'px-3 py-2.5',
                        $page.props.current_route === 'sharedWithMe'
                            ? 'bg-canvas-soft text-ink font-medium'
                            : 'text-ink-mute hover:bg-canvas-soft hover:text-ink'
                    ]"
                    :title="collapsed ? 'Shared With Me' : ''"
                    @click="handleNavClick"
                >
                    <UsersIcon :class="['flex-shrink-0 transition-all duration-300', collapsed ? 'w-6 h-6' : 'w-5 h-5 mr-3', $page.props.current_route === 'sharedWithMe' ? 'text-ink' : 'text-ink-mute group-hover:text-ink']" />
                    <span v-if="!collapsed" class="font-sans text-body-md whitespace-nowrap">Shared With Me</span>
                </Link>

                <Link
                    :href="route('sharedByMe')"
                    :class="[
                        'flex items-center rounded-sm transition-all duration-200 group',
                        collapsed ? 'justify-center px-0 py-2.5' : 'px-3 py-2.5',
                        $page.props.current_route === 'sharedByMe'
                            ? 'bg-canvas-soft text-ink font-medium'
                            : 'text-ink-mute hover:bg-canvas-soft hover:text-ink'
                    ]"
                    :title="collapsed ? 'Shared By Me' : ''"
                    @click="handleNavClick"
                >
                    <ShareIcon :class="['flex-shrink-0 transition-all duration-300', collapsed ? 'w-6 h-6' : 'w-5 h-5 mr-3', $page.props.current_route === 'sharedByMe' ? 'text-ink' : 'text-ink-mute group-hover:text-ink']" />
                    <span v-if="!collapsed" class="font-sans text-body-md whitespace-nowrap">Shared By Me</span>
                </Link>

                <Link
                    :href="route('trash')"
                    :class="[
                        'flex items-center rounded-sm transition-all duration-200 group',
                        collapsed ? 'justify-center px-0 py-2.5' : 'px-3 py-2.5',
                        $page.props.current_route === 'trash'
                            ? 'bg-canvas-soft text-ink font-medium'
                            : 'text-ink-mute hover:bg-canvas-soft hover:text-ink'
                    ]"
                    :title="collapsed ? 'Trash' : ''"
                    @click="handleNavClick"
                >
                    <TrashIcon :class="['flex-shrink-0 transition-all duration-300', collapsed ? 'w-6 h-6' : 'w-5 h-5 mr-3', $page.props.current_route === 'trash' ? 'text-ink' : 'text-ink-mute group-hover:text-ink']" />
                    <span v-if="!collapsed" class="font-sans text-body-md whitespace-nowrap">Trash</span>
                </Link>
            </nav>
            </div><!-- end overflow-x-hidden nav links -->
        </div>

        <!-- Collapse Toggle Button -->
        <button
            @click="$emit('toggle-collapse')"
            class="absolute -right-3 top-[88px] w-6 h-6 bg-canvas border border-hairline rounded-full flex items-center justify-center shadow-sm hover:bg-canvas-soft hover:border-hairline-strong transition-all duration-200 z-10"
            :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
        >
            <ChevronLeftIcon v-if="!collapsed" class="w-3.5 h-3.5 text-ink-mute" />
            <ChevronRightSideIcon v-else class="w-3.5 h-3.5 text-ink-mute" />
        </button>
    </nav>

    <!-- Mobile Sidebar -->
    <nav
        :class="[
            'fixed inset-y-0 left-0 z-50 w-72 bg-canvas border-r border-hairline shadow-elevation-3 transform transition-transform duration-300 ease-in-out lg:hidden',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <!-- Mobile Header -->
        <div class="relative flex items-center justify-center py-12 border-b border-hairline">
            <Link :href="route('myFiles')" @click="handleNavClick" class="flex items-center justify-center w-full">
                <ApplicationLogo class="h-14 w-auto" />
            </Link>
            <button
                @click="$emit('close')"
                class="absolute right-2 p-2 rounded-sm hover:bg-canvas-soft active:bg-hairline-cool transition-colors"
                aria-label="Close menu"
            >
                <XMarkIcon class="w-6 h-6 text-ink" />
            </button>
        </div>

        <!-- Mobile Navigation Content -->
        <div class="flex-1 flex flex-col overflow-y-auto h-[calc(100vh-5rem)]">
            <div class="px-4 py-4">
                <CreateNewDropdown />
            </div>

            <nav class="flex-1 px-3 py-2 space-y-1">
                <Link
                    :href="route('myFiles')"
                    :class="[
                        'flex items-center px-3 py-2.5 rounded-sm transition-all duration-200 group',
                        $page.props.current_route === 'myFiles'
                            ? 'bg-canvas-soft text-ink font-medium'
                            : 'text-ink-mute hover:bg-canvas-soft'
                    ]"
                    @click="handleNavClick"
                >
                    <FolderIcon class="w-5 h-5 mr-3 flex-shrink-0" />
                    <span class="font-sans text-body-md">My Files</span>
                </Link>

                <Link
                    :href="route('sharedWithMe')"
                    :class="[
                        'flex items-center px-3 py-2.5 rounded-sm transition-all duration-200 group',
                        $page.props.current_route === 'sharedWithMe'
                            ? 'bg-canvas-soft text-ink font-medium'
                            : 'text-ink-mute hover:bg-canvas-soft'
                    ]"
                    @click="handleNavClick"
                >
                    <UsersIcon class="w-5 h-5 mr-3 flex-shrink-0" />
                    <span class="font-sans text-body-md">Shared With Me</span>
                </Link>

                <Link
                    :href="route('sharedByMe')"
                    :class="[
                        'flex items-center px-3 py-2.5 rounded-sm transition-all duration-200 group',
                        $page.props.current_route === 'sharedByMe'
                            ? 'bg-canvas-soft text-ink font-medium'
                            : 'text-ink-mute hover:bg-canvas-soft'
                    ]"
                    @click="handleNavClick"
                >
                    <ShareIcon class="w-5 h-5 mr-3 flex-shrink-0" />
                    <span class="font-sans text-body-md">Shared By Me</span>
                </Link>

                <Link
                    :href="route('trash')"
                    :class="[
                        'flex items-center px-3 py-2.5 rounded-sm transition-all duration-200 group',
                        $page.props.current_route === 'trash'
                            ? 'bg-canvas-soft text-ink font-medium'
                            : 'text-ink-mute hover:bg-canvas-soft'
                    ]"
                    @click="handleNavClick"
                >
                    <TrashIcon class="w-5 h-5 mr-3 flex-shrink-0" />
                    <span class="font-sans text-body-md">Trash</span>
                </Link>
            </nav>
        </div>
    </nav>
</template>
