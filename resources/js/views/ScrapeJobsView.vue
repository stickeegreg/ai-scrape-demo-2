<script setup>
import { ref, onMounted } from 'vue';
import { api } from '../include/api';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import LinkTo from '../components/LinkTo.vue';

const loading = ref(true);
const error = ref(null);
const scrapeJobs = ref([]);

onMounted(async () => {
    loading.value = true;
    error.value = null;
    scrapeJobs.value = [];

    try {
        scrapeJobs.value = await api.listScrapeJobs();
    } catch (error) {
        console.error(error);
        error.value = error.message;
    } finally {
        loading.value = false;
    }
});

const deleteScrapeJob = async (scrapeJob) => {
    if (!confirm(`Are you sure you want to delete the scrapeJob "${scrapeJob.name}"?`)) {
        return;
    }

    try {
        await api.deleteScrapeJob(scrapeJob.id);
        scrapeJobs.value = scrapeJobs.value.filter((w) => w.id !== scrapeJob.id);
    } catch (error) {
        console.error(error);
        error.value = error.message;
        alert(`Failed to delete the scrapeJob "${scrapeJob.name}".`);
    }
};
</script>

<template>
    <DefaultLayout>
        <h1 class="mb-4 text-4xl font-bold text-gray-900">Scrape Jobs</h1>
        {{ error }}

        <div v-if="loading">Loading...</div>
        <div v-else>
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                    <tr>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">ID</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Name</th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="scrapeJob in scrapeJobs" :key="scrapeJob.id">
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeJob.id }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeJob.name }}</td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <LinkTo class="pr-2" :to="`/scrape-jobs/${scrapeJob.id}`">Edit</LinkTo>
                            <LinkTo @click.prevent="deleteScrapeJob(scrapeJob)">Delete</LinkTo>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </DefaultLayout>
</template>
