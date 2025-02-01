<script setup>
import NavigationPanel from '../components/NavigationPanel.vue'
</script>

<template>
    <div class="flex">
        <div>
            <NavigationPanel />
        </div>
        <div class="flex-1">
            <slot />
        </div>
    </div>
</template>
