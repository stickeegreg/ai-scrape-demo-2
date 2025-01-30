<script setup>
const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    variant: {
        type: String,
        required: false,
        default: 'primary',
    },
    type: {
        type: String,
        required: false,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        required: false,
        default: false,
    },
});
defineEmits(['click']);
</script>

<template>
    <button
        class="text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
        :class="{
            'bg-blue-500': variant === 'primary',
            'hover:bg-blue-700': variant === 'primary',
            'bg-red-500': variant === 'danger',
            'hover:bg-red-700': variant === 'danger',
            'opacity-50 cursor-not-allowed': disabled,
        }"
        :type="type"
        @click="$emit('click')"
        :disabled="disabled"
    >
        {{ label }}
    </button>
</template>
