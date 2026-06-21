<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-ink bg-canvas-soft text-start font-sans text-body-md text-ink focus:outline-none focus:text-ink focus:bg-canvas focus:border-ink transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start font-sans text-body-md text-ink-mute hover:text-ink hover:bg-canvas-soft hover:border-hairline-strong focus:outline-none focus:text-ink focus:bg-canvas-soft focus:border-hairline-strong transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
