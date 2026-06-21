<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
</script>

<template>
    <Menu as="div" class="relative inline-block text-left z-10">
        <div>
            <MenuButton
                class="inline-flex w-full items-center justify-center rounded-sm px-2 sm:px-4 py-2 font-sans text-body-md font-medium text-ink focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/75 hover:bg-canvas-soft transition-colors"
            >
                <span class="truncate max-w-[100px] sm:max-w-none">{{ $page.props.auth.user.name }}</span>
                <ChevronDownIcon
                    class="-mr-1 ml-1 sm:ml-2 h-4 w-4 sm:h-5 sm:w-5 text-ink flex-shrink-0"
                    aria-hidden="true"
                />
            </MenuButton>
        </div>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-hairline rounded-sm bg-canvas shadow-elevation-2 ring-1 ring-hairline focus:outline-none"
            >
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <ResponsiveNavLink
                            :href="route('profile.edit')"
                            :class="[
                                active
                                    ? 'bg-canvas-soft text-ink'
                                    : 'text-ink-mute',
                                'block px-4 py-2 font-sans text-body-md',
                            ]"
                        >
                            Profile
                        </ResponsiveNavLink>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <ResponsiveNavLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                            :class="[
                                active
                                    ? 'bg-canvas-soft text-ink'
                                    : 'text-ink-mute',
                                'block px-4 py-2 font-sans text-body-md',
                            ]"
                        >
                            Logout
                        </ResponsiveNavLink>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
