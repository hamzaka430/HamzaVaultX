<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-canvas border border-hairline-strong rounded-sm font-sans text-button-md text-ink hover:bg-canvas-soft focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
