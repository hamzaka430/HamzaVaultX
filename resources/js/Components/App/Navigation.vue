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
    <nav
        class="hidden lg:flex lg:flex-col bg-white border-r border-gray-200 w-56 xl:w-64"
    >
        <Link
            :href="route('myFiles')"
            class="h-36 xl:h-44 flex items-center justify-center"
        >
            <ApplicationLogo class="h-32 xl:h-40" />
        </Link>

        <div class="px-3 flex-1 overflow-y-auto">
            <CreateNewDropdown />

            <div class="py-3 space-y-1">
                <NavLink
                    :href="route('myFiles')"
                    :active="$page.props.current_route === 'myFiles'"
                    >My Files</NavLink
                >
                <NavLink
                    :href="route('sharedWithMe')"
                    :active="$page.props.current_route === 'sharedWithMe'"
                    >Shared With Me</NavLink
                >
                <NavLink
                    :href="route('sharedByMe')"
                    :active="$page.props.current_route === 'sharedByMe'"
                    >Shared By Me</NavLink
                >
                <NavLink
                    :href="route('trash')"
                    :active="$page.props.current_route === 'trash'"
                    >Trash</NavLink
                >
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar -->
    <nav
        :class="[
            'fixed inset-y-0 left-0 z-50 w-64 bg-white transform transition-transform duration-300 ease-in-out lg:hidden',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <div class="flex items-center justify-between h-40">
            <Link :href="route('myFiles')" @click="handleNavClick">
                <ApplicationLogo class="h-36" />
            </Link>
            <button
                @click="$emit('close')"
                class="p-2 rounded-md hover:bg-gray-100 transition-colors"
            >
                <XMarkIcon class="w-6 h-6 text-gray-700" />
            </button>
        </div>

        <div class="px-3 overflow-y-auto h-[calc(100vh-10rem)]">
            <CreateNewDropdown />

            <div class="py-3 space-y-1">
                <NavLink
                    :href="route('myFiles')"
                    :active="$page.props.current_route === 'myFiles'"
                    @click="handleNavClick"
                    >My Files</NavLink
                >
                <NavLink
                    :href="route('sharedWithMe')"
                    :active="$page.props.current_route === 'sharedWithMe'"
                    @click="handleNavClick"
                    >Shared With Me</NavLink
                >
                <NavLink
                    :href="route('sharedByMe')"
                    :active="$page.props.current_route === 'sharedByMe'"
                    @click="handleNavClick"
                    >Shared By Me</NavLink
                >
                <NavLink
                    :href="route('trash')"
                    :active="$page.props.current_route === 'trash'"
                    @click="handleNavClick"
                    >Trash</NavLink
                >
            </div>
        </div>
    </nav>
</template>
