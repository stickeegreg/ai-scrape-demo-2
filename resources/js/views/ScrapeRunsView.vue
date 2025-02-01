<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { api } from '../include/api';
import { scrapeStrategies } from '../include/scrape-ais';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import LinkTo from '../components/LinkTo.vue';

const loading = ref(false);
const initialLoad = ref(true);
const error = ref(null);
const scrapeRuns = ref([]);

const update = async () => {
    if (loading.value) {
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        scrapeRuns.value = await api.listScrapeRuns();
    } catch (error) {
        console.error(error);
        error.value = error.message;
    } finally {
        loading.value = false;
    }
};

let updateInterval;
const onFocus = () => {
    clearInterval(updateInterval);
    updateInterval = setInterval(update, 2000);
};
const onBlur = () => {
    clearInterval(updateInterval);
};

onMounted(async () => {
    await update();
    initialLoad.value = false;

    window.addEventListener('focus', onFocus);
    window.addEventListener('blur', onBlur);

    updateInterval = setInterval(update, 2000);
});

onUnmounted(() => {
    window.removeEventListener('focus', onFocus);
    window.removeEventListener('blur', onBlur);

    clearInterval(updateInterval);
});

const deleteScrapeRun = async (scrapeRun) => {
    if (!confirm(`Are you sure you want to delete the scrapeRun "${scrapeRun.name}"?`)) {
        return;
    }

    try {
        await api.deleteScrapeRun(scrapeRun.id);
        scrapeRuns.value = scrapeRuns.value.filter((w) => w.id !== scrapeRun.id);
    } catch (error) {
        console.error(error);
        error.value = error.message;
        alert(`Failed to delete the scrapeRun "${scrapeRun.name}".`);
    }
};
</script>

<template>
    <DefaultLayout>
        <h1 class="mb-4 text-4xl font-bold text-gray-900">Scrape Runs</h1>
        {{ error }}

        <div v-if="loading && initialLoad">Loading...</div>
        <div v-else>
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                    <tr>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">ID</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Website</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Scrape Type</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">URL</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">AI</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Status</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="scrapeRun in scrapeRuns" :key="scrapeRun.id">
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeRun.id }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeRun.scrape.website.name }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeRun.scrape.scrape_type.name }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeRun.scrape.url }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeStrategies[scrapeRun.scrape.class] }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeRun.status }}</td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <LinkTo class="pr-2" :to="`/scrape-runs/${scrapeRun.id}`">Edit</LinkTo>
                            <LinkTo @click.prevent="deleteScrapeRun(scrapeRun)">Delete</LinkTo>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </DefaultLayout>
</template>
