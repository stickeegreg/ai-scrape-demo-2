<script setup>
import { ref, onMounted } from 'vue';
import { api } from '../include/api';
import { scrapeStrategies } from '../include/scrape-strategies';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import LinkTo from '../components/LinkTo.vue';

const loading = ref(true);
const error = ref(null);
const scrapes = ref([]);
const startingRun = ref(false);

onMounted(async () => {
    loading.value = true;
    error.value = null;
    scrapes.value = [];

    try {
        scrapes.value = await api.listScrapes();
    } catch (error) {
        console.error(error);
        error.value = error.message;
    } finally {
        loading.value = false;
    }
});

const deleteScrape = async (scrape) => {
    if (!confirm(`Are you sure you want to delete the scrape "${scrape.name}"?`)) {
        return;
    }

    try {
        await api.deleteScrape(scrape.id);
        scrapes.value = scrapes.value.filter((w) => w.id !== scrape.id);
    } catch (error) {
        console.error(error);
        error.value = error.message;
        alert(`Failed to delete the scrape "${scrape.name}".`);
    }
};

const runScrape = async (scrape) => {
    startingRun.value = true;

    try {
        await api.runScrape(scrape.id);
    } catch (error) {
        console.error(error);
        error.value = error.message;
        alert(`Failed to run scrape "${scrape.id}".`);
    } finally {
        startingRun.value = false;
    }
};
</script>

<template>
    <DefaultLayout>
        <h1 class="mb-4 text-4xl font-bold text-gray-900">Scrapes</h1>
        {{ error }}

        <div v-if="loading">Loading...</div>
        <div v-else>
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                    <tr>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">ID</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Website</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Scrape Type</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">AI</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="scrape in scrapes" :key="scrape.id">
                        <td class="p-4 border-b border-blue-gray-50">{{ scrape.id }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ scrape.website.name }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ scrape.scrape_type.name }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeStrategies[scrape.strategy] }}</td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <LinkTo class="pr-2" :to="`/scrapes/${scrape.id}`">Edit</LinkTo>
                            <LinkTo variant="danger" class="pr-2" @click.prevent="deleteScrape(scrape)">Delete</LinkTo>
                            <LinkTo @click.prevent="runScrape(scrape)" :class="{'opacity-50 cursor-not-allowed': startingRun }">Run</LinkTo>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4">
                <LinkTo to="/scrapes/create">Add Scrape</LinkTo>
            </div>
        </div>
    </DefaultLayout>
</template>
