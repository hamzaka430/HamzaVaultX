<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 px-4 sm:px-6 lg:px-8 py-12 lg:pt-0 lg:pb-12">
        <!-- Logo Section -->
        <div>
            <Link href="/">
                <ApplicationLogo class="w-40 sm:w-48 md:w-56 h-auto" />
            </Link>
        </div>

        <!-- Form Card -->
        <div class="w-full max-w-sm">
            <div class="bg-white shadow-xl shadow-gray-200/50 rounded-2xl px-6 py-6 sm:px-8 sm:py-8">
                <slot />
            </div>

            <!-- Additional Links -->
            <div class="mt-4 text-center">
                <slot name="registerLink"></slot>
            </div>
        </div>
    </div>
</template>
