<script setup>
import { ref, onMounted } from 'vue';
import { api } from '../include/api';
import DefaultLayout from '../layouts/DefaultLayout.vue';

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
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="website in websites" :key="website.id">
                        <td>{{ website.name }}</td>
                        <td>
                            <RouterLink :to="`/websites/${website.id}`">Edit</RouterLink>
                            <button @click="deleteWebsite(website)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </DefaultLayout>
</template>
