<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { api } from '../include/api';
import RFB from '@novnc/novnc';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import ActionButton from '../components/ActionButton.vue';
import ScrapeRunMessages from '../components/ScrapeRunMessages.vue';

const route = useRoute()
const id = ref(route.params.id);
const loading = ref(true);
const error = ref(null);
const scrapeRun = ref(null);
const viewOnly = ref(true);
const vncContainer = ref(null);
const screenshots = ref([]);
const screenshotCanvas = ref(null);
let rfb = null;

watch(
  () => route.params.id,
  (newId, oldId) => {
    id.value = newId;

    loadScrapeRun();
  }
);

watch(viewOnly, (newViewOnly, oldViewOnly) => {
    rfb.viewOnly = newViewOnly;
});

const loadScrapeRun = async () => {
    loading.value = true;
    error.value = null;
    scrapeRun.value = null;

    try {
        scrapeRun.value = await api.getScrapeRun(id.value);
    } catch (error) {
        console.error(error);
        error.value = error.message;
    } finally {
        loading.value = false;
    }
};

const screenshot = async () => {
    try {
        const imageData = rfb.getImageData();

        screenshotCanvas.value.width = imageData.width;
        screenshotCanvas.value.height = imageData.height;

        const ctx = screenshotCanvas.value.getContext('2d');
        ctx.putImageData(imageData, 0, 0);

        screenshots.value.push(screenshotCanvas.value.toDataURL());
    } catch (error) {
        console.error(error);
    }
};

onMounted(async () => {
    await loadScrapeRun();

    rfb = new RFB(vncContainer.value, `ws://${scrapeRun.value.data.no_vnc_address}`);
    rfb.scaleViewport = true;
    rfb.viewOnly = viewOnly.value;
});
</script>

<template>
    <DefaultLayout>
        <h1 class="mb-4 text-4xl font-bold text-gray-900">Scrape Run {{ id }}</h1>
        {{ error }}

        <div v-if="loading">Loading...</div>
        <div v-else>
            Status: {{ scrapeRun.status }}
            <!-- <pre>{{ JSON.stringify((scrapeRun.data), null, 2) }}</pre> -->

            <ScrapeRunMessages :messages="scrapeRun.data.messages || []" />

            <ActionButton @click="viewOnly = !viewOnly" :label="viewOnly ? 'Enable Control' : 'View Only'" />
            <ActionButton @click="screenshot" label="Capture Screenshot" />
            <div ref="vncContainer" class="w-full h-screen"></div>
            <canvas ref="screenshotCanvas" class="hidden"></canvas>
            <div v-for="screenshot in screenshots">
                <img :src="screenshot" />
            </div>
        </div>
    </DefaultLayout>
</template>
