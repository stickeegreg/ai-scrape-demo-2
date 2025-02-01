<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { api } from '../include/api';
import { ValidationError } from '../include/validation-error';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import ActionButton from '../components/ActionButton.vue';
import TextInput from '../components/TextInput.vue';

const route = useRoute()
const router = useRouter()
const id = ref(route.params.id);
const loading = ref(true);
const error = ref(null);
const errors = ref(null);
const scrapeRun = ref(null);

watch(
  () => route.params.id,
  (newId, oldId) => {
    id.value = newId;

    loadScrapeRun();
  }
);

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

onMounted(async () => {
    loadScrapeRun();
});
</script>

<template>
    <DefaultLayout>
        <h1 class="mb-4 text-4xl font-bold text-gray-900">Scrape Run {{ id }}</h1>
        {{ error }}

        <div v-if="loading">Loading...</div>
        <div v-else>
            Status: {{ scrapeRun.status }}
            <pre>{{ JSON.stringify((scrapeRun.data), null, 2) }}</pre>
        </div>
    </DefaultLayout>
</template>
