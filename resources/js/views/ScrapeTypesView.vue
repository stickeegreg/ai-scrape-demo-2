<script setup>
import { ref, onMounted } from 'vue';
import { api } from '../include/api';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import LinkTo from '../components/LinkTo.vue';

const loading = ref(true);
const error = ref(null);
const scrapeTypes = ref([]);

onMounted(async () => {
    loading.value = true;
    error.value = null;
    scrapeTypes.value = [];

    try {
        scrapeTypes.value = await api.listScrapeTypes();
    } catch (error) {
        console.error(error);
        error.value = error.message;
    } finally {
        loading.value = false;
    }
});

const deleteScrapeType = async (scrapeType) => {
    if (!confirm(`Are you sure you want to delete the scrapeType "${scrapeType.name}"?`)) {
        return;
    }

    try {
        await api.deleteScrapeType(scrapeType.id);
        scrapeTypes.value = scrapeTypes.value.filter((w) => w.id !== scrapeType.id);
    } catch (error) {
        console.error(error);
        error.value = error.message;
        alert(`Failed to delete the scrapeType "${scrapeType.name}".`);
    }
};
</script>

<template>
    <DefaultLayout>
        <h1 class="mb-4 text-4xl font-bold text-gray-900">Scrape Types</h1>
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
                    <tr v-for="scrapeType in scrapeTypes" :key="scrapeType.id">
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeType.id }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ scrapeType.name }}</td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <LinkTo class="pr-2" :to="`/scrape-types/${scrapeType.id}`">Edit</LinkTo>
                            <LinkTo variant="danger" @click.prevent="deleteScrapeType(scrapeType)">Delete</LinkTo>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4">
                <LinkTo to="/scrape-types/create">Add Scrape Type</LinkTo>
            </div>
        </div>
    </DefaultLayout>
</template>
