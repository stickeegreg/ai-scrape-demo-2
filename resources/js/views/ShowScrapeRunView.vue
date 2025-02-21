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
            <div class="p-2 mb-2">
                Status:
                <span class="p-2 rounded text-white font-bold" :class="{'bg-gray-400': (scrapeRun.status !== 'failed') && (scrapeRun.status !== 'completed'), 'bg-red-600': scrapeRun.status === 'failed', 'bg-green-600': scrapeRun.status === 'completed'}">
                    {{ scrapeRun.status }}
                </span>
            </div>
            <!-- <pre>{{ JSON.stringify((scrapeRun.data), null, 2) }}</pre> -->

            <ScrapeRunMessages :messages="scrapeRun.data.messages || []" />
            <div v-if="scrapeRun.data.error">
                <h2 class="text-2xl font-bold text-gray-900">Error</h2>
                <div class="bg-red-200 p-2 rounded">{{ scrapeRun.data.error }}</div>
            </div>

            <div v-if="scrapeRun.data.result" class="mt-2">
                <h2 class="text-2xl font-bold text-gray-900">Result</h2>
                <pre class="bg-gray-200 p-2 rounded">{{ JSON.stringify(scrapeRun.data.result, null, 2) }}</pre>
            </div>

            <div class="mt-2">
                <ActionButton @click="viewOnly = !viewOnly" :label="viewOnly ? 'Enable Control' : 'View Only'" class="mr-2" />
                <ActionButton @click="screenshot" label="Capture Screenshot" />
            </div>

            <video v-if="scrapeRun.data.recording" controls class="mt-2">
                <source :src="`/storage/${scrapeRun.data.recording}`" type="video/webm" />
            </video>

            <div ref="vncContainer" class="w-full h-screen mt-2"></div>
            <canvas ref="screenshotCanvas" class="hidden"></canvas>
            <div v-for="screenshot in screenshots">
                <img :src="screenshot" />
            </div>
        </div>
    </DefaultLayout>
</template>
