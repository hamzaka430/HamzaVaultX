<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-gray-50 to-gray-100 px-4 sm:px-6 lg:px-8 py-8 sm:py-12 md:py-16 lg:py-20">
        <div class="flex flex-col md:flex-row items-center justify-center w-full max-w-4xl gap-8 md:gap-12 lg:gap-16 xl:gap-24">
            <!-- Logo Section -->
            <div class="flex justify-center w-full md:w-1/2">
                <Link href="/" class="flex justify-center md:justify-end items-center">  
                    <ApplicationLogo class="w-32 sm:w-40 md:w-48 lg:w-56 xl:w-64 h-auto drop-shadow-sm object-contain" />
                </Link>
            </div>

            <!-- Form Card -->
            <div class="w-full max-w-sm md:w-1/2">
                <div class="bg-white shadow-xl shadow-gray-200/50 rounded-2xl px-6 py-6 sm:px-8 sm:py-8">
                    <slot />
                </div>

                <!-- Additional Links -->
                <div class="mt-4 text-center">
                    <slot name="registerLink"></slot>
                </div>
            </div>
        </div>
    </div>
</template>
