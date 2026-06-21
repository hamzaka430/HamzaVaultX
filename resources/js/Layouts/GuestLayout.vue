<script setup>
import { Link } from "@inertiajs/vue3";
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center bg-canvas-soft px-4 sm:px-6 lg:px-8 py-8 sm:py-12 md:py-16 lg:py-20">
        <div class="flex flex-col md:flex-row items-center justify-center w-full max-w-4xl gap-8 md:gap-12 lg:gap-16 xl:gap-24">
            <!-- Logo Section -->
            <div class="flex justify-center w-full md:w-1/2">
                <Link href="/" class="flex justify-center md:justify-end items-center">  
                    <ApplicationLogo class="w-48 h-auto sm:w-64" />
                </Link>
            </div>

            <!-- Form Card -->
            <div class="w-full max-w-sm md:w-1/2">
                <div class="bg-canvas shadow-elevation-2 border border-hairline rounded-xl px-6 py-6 sm:px-8 sm:py-8">
                    <slot />

                    <!-- Additional Links -->
                    <div class="mt-6 text-center">
                        <slot name="registerLink"></slot>
                    </div>

                    <!-- Developed By Text -->
                    <div class="mt-6 pt-4 border-t border-hairline text-center">
                        <p class="font-sans text-caption text-ink-mute">
                            Developed by <a href="https://dezignwise.online" target="_blank" class="text-ink border-b border-ink hover:text-primary hover:border-primary transition-colors">DezignWise</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>


    </div>
</template>
