<script setup>
const props = defineProps({
    tasks: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);
</script>

<template>
    <div
        v-if="tasks.length > 0"
        class="absolute w-[320px] max-h-[400px] overflow-y-auto flex flex-col bg-white rounded shadow-lg border border-gray-200 right-8 bottom-4"
    >
        <div class="p-3 border-b bg-gray-50 sticky top-0 z-10 shadow-sm">
            <div class="flex justify-between items-center mb-2">
                <span class="font-semibold text-sm text-gray-800">Uploading {{ tasks.length }} file(s)</span>
                <button @click="emit('close')" class="text-gray-500 hover:text-gray-800 text-lg leading-none">
                    &times;
                </button>
            </div>
            <!-- Overall Progress -->
            <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div 
                    class="h-full bg-indigo-600 transition-all duration-300"
                    :style="{ width: Math.round(tasks.reduce((acc, task) => acc + task.progress, 0) / tasks.length) + '%' }"
                ></div>
            </div>
            <div class="text-xs text-gray-500 mt-1 text-right">
                {{ Math.round(tasks.reduce((acc, task) => acc + task.progress, 0) / tasks.length) }}%
            </div>
        </div>
        <div class="p-3 space-y-3">
            <div v-for="task in tasks" :key="task.id" class="text-xs">
                <div class="flex justify-between mb-1 truncate">
                    <span class="truncate pr-2 font-medium" :title="task.file.name">{{ task.file.name }}</span>
                    <span v-if="task.status === 'uploading'" class="text-gray-500 shrink-0">{{ task.progress }}%</span>
                    <span v-else-if="task.status === 'pending'" class="text-gray-400 shrink-0">Pending...</span>
                    <span v-else-if="task.status === 'success'" class="text-green-600 font-bold shrink-0">Done</span>
                    <span v-else class="text-red-600 font-bold shrink-0">Failed</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-md overflow-hidden relative">
                    <div
                        v-if="task.status !== 'error'"
                        class="h-full bg-indigo-600 transition-all duration-300"
                        :class="{'bg-green-500': task.status === 'success'}"
                        :style="{ width: task.progress + '%' }"
                    ></div>
                    <div v-else class="h-full bg-red-500 w-full"></div>
                </div>
                <div v-if="task.error" class="text-red-500 mt-1 truncate" :title="task.error">
                    {{ task.error }}
                </div>
            </div>
        </div>
    </div>
</template>
