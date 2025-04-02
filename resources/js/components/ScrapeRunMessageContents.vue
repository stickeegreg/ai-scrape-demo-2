<script setup>
const props = defineProps({
    contents: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div v-for="content in contents" class="mt-1">
        <img v-if="content.type === 'image'" class="inline" :src="`data:${content.source.media_type};${content.source.type},${content.source.data}`" />
        <img v-if="content.type === 'image_url'" class="inline" :src="`${content.image_url.url}`" />
        <div v-else-if="content.type === 'text'">{{ content.text }}</div>
        <div v-else-if="content.type === 'tool_use'">
            Using the <b>{{ content.name }}</b> tool with input: {{ content.input }}
        </div>
        <div v-else-if="content.type === 'tool_result'">
            Tool use {{ content.is_error ? 'failed' : 'succeeded' }}

            <ScrapeRunMessageContents :contents="content.content" />
        </div>
        <div v-else>Unknown content: <pre>{{ content }}</pre></div>
    </div>
</template>
