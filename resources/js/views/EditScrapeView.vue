<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { api } from '../include/api';
import { ValidationError } from '../include/validation-error';
import { scrapeStrategies } from '../include/scrape-strategies';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import ActionButton from '../components/ActionButton.vue';
import SelectInput from '../components/SelectInput.vue';
import TextInput from '../components/TextInput.vue';

const props = defineProps({
    create: {
        type: Boolean,
        required: false,
        default: false,
    },
});

const route = useRoute()
const router = useRouter()
const id = ref(route.params.id);
const loading = ref(true);
const error = ref(null);
const errors = ref(null);
const scrape = ref(null);
const saving = ref(false);
const websites = ref([]);
const scrapeTypes = ref([]);
const strategyOptions = Object.entries(scrapeStrategies).map(([key, value]) => ({ value: key, label: value }));

watch(
  () => route.params.id,
  (newId, oldId) => {
    id.value = newId;

    loadScrape();
  }
);

const websiteOptions = computed(() => websites.value.map((w) => ({ value: w.id, label: w.name })));
const scrapeTypeOptions = computed(() => scrapeTypes.value.map((s) => ({ value: s.id, label: s.name })));

const loadScrape = async () => {
    loading.value = true;
    error.value = null;
    scrape.value = null;

    try {
        scrape.value = await api.getScrape(id.value);
    } catch (error) {
        console.error(error);
        error.value = error.message;
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    if (props.create) {
        loading.value = false;
        scrape.value = {
            name: '',
            url: '',
            prompt: '',
        };
    } else {
        loadScrape();
    }

    try {
        websites.value = await api.listWebsites();
        scrapeTypes.value = await api.listScrapeTypes();
    } catch (error) {
        console.error(error);
        error.value = error.message;
    } finally {
        loading.value = false;
    }
});

const deleteScrape = async () => {
    if (!confirm(`Are you sure you want to delete the scrape "${scrape.value.name}"?`)) {
        return;
    }

    try {
        await api.deleteScrape(scrape.value.id);
        scrapes.value = scrapes.value.filter((w) => w.id !== scrape.id);
    } catch (error) {
        console.error(error);
        error.value = error.message;
        alert(`Failed to delete the scrape "${scrape.value.name}".`);
    }
};

const saveScrape = async () => {
    error.value = null;
    errors.value = null;
    saving.value = true;

    try {
        if (props.create) {
            const newScrape = await api.createScrape(scrape.value);
            router.push(`/scrapes/${newScrape.id}`);
        } else {
            await api.updateScrape(scrape.value);
        }

        alert('Scrape saved.');
    } catch (error) {
        if (error instanceof ValidationError) {
            error.value = error.message;
            errors.value = error.errors;
            return;
        }

        console.error(error);
        error.value = error.message;
    } finally {
        saving.value = false;
    }
};
</script>

<template>
    <DefaultLayout>
        <h1 class="mb-4 text-4xl font-bold text-gray-900">{{ create ? 'Create' : 'Edit' }} Scrape</h1>
        {{ error }}

        <div v-if="loading">Loading...</div>
        <div v-else>
            <form
                class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4"
                @submit.prevent="saveScrape"
            >
                <SelectInput
                    name="website_id"
                    label="Website"
                    v-model="scrape.website_id"
                    :error="errors?.website_id?.[0]"
                    required
                    :options="websiteOptions"
                />
                <SelectInput
                    name="scrape_type_id"
                    label="Scrape Type"
                    v-model="scrape.scrape_type_id"
                    :error="errors?.scrape_type_id?.[0]"
                    required
                    :options="scrapeTypeOptions"
                />
                <TextInput
                    name="url"
                    label="URL"
                    placeholder="https://example.com"
                    v-model="scrape.url"
                    :error="errors?.url?.[0]"
                    required
                />
                <TextInput
                    name="prompt"
                    label="Custom Prompt"
                    placeholder="Custom prompt"
                    v-model="scrape.prompt"
                    :error="errors?.prompt?.[0]"
                    type="textarea"
                />
                <SelectInput
                    name="strategy"
                    label="AI Type"
                    v-model="scrape.strategy"
                    :error="errors?.strategy?.[0]"
                    required
                    :options="strategyOptions"
                />
                <div class="flex items-center justify-between">
                    <ActionButton label="Save" type="submit" :disabled="saving" />
                    <ActionButton v-if="!create" label="Delete" variant="danger" @click="deleteScrape" :disabled="saving" />
                </div>
            </form>
        </div>
    </DefaultLayout>
</template>
