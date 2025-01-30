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
    type: {
        type: String,
        required: false,
        default: 'text',
    },
    placeholder: {
        type: String,
        required: false,
        default: '',
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

        <textarea
            v-if="type === 'textarea'"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
            :class="{ 'border-red-500': error }"
            :id="name"
            :type="type"
            :placeholder="placeholder"
            v-model="model"
            :required="required"
        ></textarea>
        <input
            v-else
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
            :class="{ 'border-red-500': error }"
            :id="name"
            :type="type"
            :placeholder="placeholder"
            v-model="model"
            :required="required"
        >
        <p class="text-red-500 text-xs italic" v-if="error">
            {{ error }}
        </p>
    </div>
</template>
