<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-sans text-h4 font-medium text-ink leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-canvas border border-hairline overflow-hidden shadow-elevation-1 sm:rounded-lg">
                    <div class="p-6 font-sans text-body-md text-ink">You're logged in!</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
