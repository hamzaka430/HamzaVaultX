<script setup>
import { Link } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import CreateNewDropdown from "@/Components/App/CreateNewDropdown.vue";
import NavLink from "@/Components/App/NavLink.vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";

defineProps({
    sidebarOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

const handleNavClick = () => {
    // Close sidebar on mobile when a nav item is clicked
    if (window.innerWidth < 1024) {
        emit("close");
    }
};
</script>

<template>
    <!-- Desktop Sidebar -->
    <nav class="hidden lg:flex lg:flex-col bg-white border-r border-gray-200 w-60 xl:w-64">
        <!-- Logo Section - Desktop -->
        <Link
            :href="route('myFiles')"
            class="flex items-center justify-center pt-5 pb-5 border-b border-gray-100"
        >
            <ApplicationLogo class="w-32 lg:w-40 xl:w-48 h-auto object-contain" />
        </Link>

        <!-- Navigation Content -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <div class="px-4 py-4">
                <CreateNewDropdown />
            </div>

            <nav class="flex-1 px-3 py-2 space-y-1">
                <NavLink
                    :href="route('myFiles')"
                    :active="$page.props.current_route === 'myFiles'"
                >
                    My Files
                </NavLink>
                <NavLink
                    :href="route('sharedWithMe')"
                    :active="$page.props.current_route === 'sharedWithMe'"
                >
                    Shared With Me
                </NavLink>
                <NavLink
                    :href="route('sharedByMe')"
                    :active="$page.props.current_route === 'sharedByMe'"
                >
                    Shared By Me
                </NavLink>
                <NavLink
                    :href="route('trash')"
                    :active="$page.props.current_route === 'trash'"
                >
                    Trash
                </NavLink>
            </nav>
        </div>
    </nav>

    <!-- Mobile Sidebar -->
    <nav
        :class="[
            'fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 shadow-2xl transform transition-transform duration-300 ease-in-out lg:hidden',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <!-- Mobile Header -->
        <div class="relative flex items-center justify-center pt-5 pb-5 border-b border-gray-100">
            <Link :href="route('myFiles')" @click="handleNavClick" class="flex items-center justify-center w-full">   
                <ApplicationLogo class="w-32 sm:w-40 h-auto object-contain" />
            </Link>
            <button
                @click="$emit('close')"
                class="absolute right-2 p-2 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-colors"
                aria-label="Close menu"
            >
                <XMarkIcon class="w-6 h-6 text-gray-600" />
            </button>
        </div>

        <!-- Mobile Navigation Content -->
        <div class="flex-1 flex flex-col overflow-y-auto h-[calc(100vh-5rem)]">
            <div class="px-4 py-4">
                <CreateNewDropdown />
            </div>

            <nav class="flex-1 px-3 py-2 space-y-1">
                <NavLink
                    :href="route('myFiles')"
                    :active="$page.props.current_route === 'myFiles'"
                    @click="handleNavClick"
                >
                    My Files
                </NavLink>
                <NavLink
                    :href="route('sharedWithMe')"
                    :active="$page.props.current_route === 'sharedWithMe'"
                    @click="handleNavClick"
                >
                    Shared With Me
                </NavLink>
                <NavLink
                    :href="route('sharedByMe')"
                    :active="$page.props.current_route === 'sharedByMe'"
                    @click="handleNavClick"
                >
                    Shared By Me
                </NavLink>
                <NavLink
                    :href="route('trash')"
                    :active="$page.props.current_route === 'trash'"
                    @click="handleNavClick"
                >
                    Trash
                </NavLink>
            </nav>
        </div>
    </nav>
</template>
