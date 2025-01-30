<script setup>
import { ref, onMounted } from 'vue';
import { api } from '../include/api';
import DefaultLayout from '../layouts/DefaultLayout.vue';

const model = defineModel();
const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        required: false,
    },
    options: {
        type: Array,
        required: true,
    },
    required: {
        type: Boolean,
        required: false,
        default: false,
    },
});


</script>

<template>
    <div class="mb-4">
        <label
            class="block text-gray-700 text-sm font-bold mb-2"
            :for="name"
        >
            {{ label }}
        </label>

        <select
            class="shadow border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
            :class="{ 'border-red-500': error }"
            :id="name"
            v-model="model"
            :required="required"
        >
            <option value="" disabled>Select {{ label }}</option>
            <option
                v-for="option in options"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>
        <p class="text-red-500 text-xs italic" v-if="error">
            {{ error }}
        </p>
    </div>
</template>
