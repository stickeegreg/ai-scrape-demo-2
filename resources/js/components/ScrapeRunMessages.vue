<script setup>
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
            <div v-for="content in message.content" class="mt-1">
                <img v-if="content.type === 'image'" class="inline" :src="`data:${content.source.media_type};${content.source.type},${content.source.data}`" />
                <div v-else-if="content.type === 'text'">{{ content.text }}</div>
                <div v-else-if="content.type === 'tool_use'">
                    Using the <b>{{ content.name }}</b> tool with input: {{ content.input }}
                </div>
                <div v-else-if="content.type === 'tool_result'">
                    Tool use {{ content.is_error ? 'failed' : 'succeeded' }}

                    <div v-for="toolUseContent in content.content">
                        <img v-if="toolUseContent.type === 'image'" class="inline" :src="`data:${toolUseContent.source.media_type};${toolUseContent.source.type},${toolUseContent.source.data}`" />
                        <div v-else-if="toolUseContent.type === 'text'">{{ toolUseContent.text }}</div>
                        <div v-else>{{ toolUseContent }}</div>
                    </div>
                </div>
                <div v-else>{{ content }}</div>
            </div>
        </div>
    </div>
</template>
