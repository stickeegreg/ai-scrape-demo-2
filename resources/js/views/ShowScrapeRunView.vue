<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue';
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
const refreshing = ref(false);
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

let refreshInterval = null;

const refreshScrapeRun = async () => {
    refreshing.value = true;
    error.value = null;

    try {
        scrapeRun.value = await api.getScrapeRun(id.value);
    } catch (error) {
        console.error(error);
        error.value = error.message;
    } finally {
        refreshing.value = false;
    }

    if (scrapeRun.value.status !== 'running') {
        clearInterval(refreshInterval);
    }
};

onMounted(async () => {
    await loadScrapeRun();

    rfb = new RFB(vncContainer.value, `ws://${scrapeRun.value.data.no_vnc_address}`);
    rfb.scaleViewport = true;
    rfb.viewOnly = viewOnly.value;

    if (scrapeRun.value.status === 'running') {
        refreshInterval = setInterval(refreshScrapeRun, 1000);
    }
});

onUnmounted(() => {
    if (rfb) {
        rfb.disconnect();
    }

    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
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

            <div v-if="scrapeRun.status === 'running'" class="flex items-center justify-center mt-2">
                <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600 mr-2" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                </svg>
                <span>Scraping...</span>
            </div>

            <div v-if="scrapeRun.data.error">
                <h2 class="text-2xl font-bold text-gray-900">Error</h2>
                <div class="bg-red-200 p-2 rounded">{{ scrapeRun.data.error }}</div>
            </div>

            <div v-if="scrapeRun.data.result" class="mt-2">
                <h2 class="text-2xl font-bold text-gray-900">Result</h2>
                <pre class="bg-gray-200 p-2 rounded">{{ JSON.stringify(scrapeRun.data.result, null, 2) }}</pre>
            </div>

            <video v-if="scrapeRun.data.recording" controls class="mt-2 mx-auto">
                <source :src="`/storage/${scrapeRun.data.recording}`" type="video/webm" />
            </video>

            <div class="mt-2">
                <ActionButton @click="viewOnly = !viewOnly" :label="viewOnly ? 'Enable Control' : 'View Only'" class="mr-2" />
                <ActionButton @click="screenshot" label="Capture Screenshot" />
            </div>

            <div ref="vncContainer" class="w-full h-screen mt-2"></div>
            <canvas ref="screenshotCanvas" class="hidden"></canvas>
            <div v-for="screenshot in screenshots">
                <img :src="screenshot" />
            </div>
        </div>
    </DefaultLayout>
</template>
