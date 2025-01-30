<script setup>
import { ref, onMounted } from 'vue';
import { api } from '../include/api';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import LinkTo from '../components/LinkTo.vue';

const loading = ref(true);
const error = ref(null);
const websites = ref([]);

onMounted(async () => {
    loading.value = true;
    error.value = null;
    websites.value = [];

    try {
        websites.value = await api.listWebsites();
    } catch (error) {
        console.error(error);
        error.value = error.message;
    } finally {
        loading.value = false;
    }
});

const deleteWebsite = async (website) => {
    if (!confirm(`Are you sure you want to delete the website "${website.name}"?`)) {
        return;
    }

    try {
        await api.deleteWebsite(website.id);
        websites.value = websites.value.filter((w) => w.id !== website.id);
    } catch (error) {
        console.error(error);
        error.value = error.message;
        alert(`Failed to delete the website "${website.name}".`);
    }
};
</script>

<template>
    <DefaultLayout>
        Websites {{ websites }}
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
                    <tr v-for="website in websites" :key="website.id">
                        <td class="p-4 border-b border-blue-gray-50">{{ website.id }}</td>
                        <td class="p-4 border-b border-blue-gray-50">{{ website.name }}</td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <LinkTo class="pr-2" :to="`/websites/${website.id}`">Edit</LinkTo>
                            <LinkTo @click.prevent="deleteWebsite(website)">Delete</LinkTo>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4">
                <LinkTo to="/websites/create">Add Website</LinkTo>
            </div>
        </div>
    </DefaultLayout>
</template>
