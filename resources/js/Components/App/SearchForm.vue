<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { MagnifyingGlassIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { ON_SEARCH, emitter } from "@/event-bus";

const searchInput = ref("");
let params = "";

const onSearch = () => {
    params.set("search", searchInput.value);
    router.get(`${window.location.pathname}?${params.toString()}`);
};

const clearSearch = () => {
    searchInput.value = "";
    params.delete("search");
    router.get(window.location.pathname);
};

onMounted(() => {
    params = new URLSearchParams(window.location.search);
    searchInput.value = params.get("search") ?? "";
    emitter.emit(ON_SEARCH, searchInput.value);
});
</script>

<template>
    <div class="flex-1 max-w-2xl h-14 sm:h-16 md:h-20 flex items-center">
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 sm:pl-4 pointer-events-none">
                <MagnifyingGlassIcon class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" />
            </div>
            <input
                type="text"
                class="block w-full pl-9 sm:pl-12 pr-10 sm:pr-12 py-2 sm:py-2.5 md:py-3 text-xs sm:text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                v-model="searchInput"
                placeholder="Search..."
                @keyup.enter.prevent="onSearch"
                autocomplete="off"
            />
            <button
                v-if="searchInput"
                @click="clearSearch"
                class="absolute inset-y-0 right-0 flex items-center pr-3 sm:pr-4 text-gray-400 hover:text-gray-600 transition"
            >
                <XMarkIcon class="w-4 h-4 sm:w-5 sm:h-5" />
            </button>
        </div>
    </div>
</template>
