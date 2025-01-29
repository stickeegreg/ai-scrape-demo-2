<script setup>
import NavigationPanel from '../components/NavigationPanel.vue'
</script>

<template>
    <div class="flex">
        <div>
            <NavigationPanel />
        </div>
        <div>
            <slot />
        </div>
    </div>
</template>
