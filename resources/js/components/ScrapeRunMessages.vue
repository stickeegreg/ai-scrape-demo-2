<script setup>
import ScrapeRunMessageContents from './ScrapeRunMessageContents.vue';

const props = defineProps({
    messages: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="bg-blue-200 p-2 rounded">
        <div v-for="message in messages" class="mt-4 first:mt-0 p-2 rounded" :class="{'bg-white': message.role === 'assistant', 'bg-blue-100': message.role === 'user', 'text-right': message.role === 'user'}">
            <ScrapeRunMessageContents :contents="message.content" />
        </div>
    </div>
</template>
