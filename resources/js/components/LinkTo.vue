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
    variant: {
        type: String,
        required: false,
        default: 'primary',
    },
});
</script>

<template>
    <RouterLink
        v-if="to"
        class="font-medium hover:underline"
        :class="{
            'text-blue-600': variant === 'primary',
            'hover:text-blue-800': variant === 'primary',
            'text-red-600': variant === 'danger',
            'hover:text-red-800': variant === 'danger',
            'opacity-50 cursor-not-allowed': disabled,
        }"
        :to="to"
    ><slot /></RouterLink>
    <a
        v-else
        class="font-medium hover:underline"
        :class="{
            'text-blue-600': variant === 'primary',
            'hover:text-blue-800': variant === 'primary',
            'text-red-600': variant === 'danger',
            'hover:text-red-800': variant === 'danger',
            'opacity-50 cursor-not-allowed': disabled,
        }"
        :href="href"
    ><slot /></a>
</template>
