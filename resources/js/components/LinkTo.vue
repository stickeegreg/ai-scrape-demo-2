<script setup>
const props = defineProps({
    to: {
        type: String,
        required: false,
        default: '',
    },
    href: {
        type: String,
        required: false,
        default: '#',
    },
});
</script>

<template>
    <RouterLink
        v-if="to"
        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
        :to="to"
    ><slot /></RouterLink>
    <a
        v-else
        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
        :href="href"
    ><slot /></a>
</template>
