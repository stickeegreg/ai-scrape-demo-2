<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { api } from '../include/api';
import { ValidationError } from '../include/validation-error';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import ActionButton from '../components/ActionButton.vue';
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
const scrapeType = ref(null);
const saving = ref(false);

watch(
  () => route.params.id,
  (newId, oldId) => {
    id.value = newId;

    loadScrapeType();
  }
);

const loadScrapeType = async () => {
    loading.value = true;
    error.value = null;
    scrapeType.value = null;

    try {
        scrapeType.value = await api.getScrapeType(id.value);
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
        scrapeType.value = {
            name: '',
            url: '',
            prompt: '',
        };
    } else {
        loadScrapeType();
    }
});

const deleteScrapeType = async () => {
    if (!confirm(`Are you sure you want to delete the scrapeType "${scrapeType.value.name}"?`)) {
        return;
    }

    try {
        await api.deleteScrapeType(scrapeType.value.id);
        scrapeTypes.value = scrapeTypes.value.filter((w) => w.id !== scrapeType.id);
    } catch (error) {
        console.error(error);
        error.value = error.message;
        alert(`Failed to delete the scrapeType "${scrapeType.value.name}".`);
    }
};

const saveScrapeType = async () => {
    error.value = null;
    errors.value = null;
    saving.value = true;

    try {
        if (props.create) {
            const newScrapeType = await api.createScrapeType(scrapeType.value);
            router.push(`/scrape-types/${newScrapeType.id}`);
        } else {
            await api.updateScrapeType(scrapeType.value);
        }

        alert('ScrapeType saved.');
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
        <h1 class="mb-4 text-4xl font-bold text-gray-900">{{ create ? 'Create' : 'Edit' }} Scrape Type</h1>
        {{ error }}

        <div v-if="loading">Loading...</div>
        <div v-else>
            <form
                class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4"
                @submit.prevent="saveScrapeType"
            >
                <TextInput
                    name="name"
                    label="Name"
                    placeholder="Name"
                    v-model="scrapeType.name"
                    :error="errors?.name?.[0]"
                    required
                />
                <TextInput
                    name="prompt"
                    label="Custom Prompt"
                    placeholder="Custom prompt"
                    v-model="scrapeType.prompt"
                    :error="errors?.prompt?.[0]"
                    type="textarea"
                />
                <TextInput
                    name="fields"
                    label="Fields"
                    placeholder="Fields (JSON)"
                    v-model="scrapeType.fields"
                    :error="errors?.fields?.[0]"
                    type="textarea"
                />
                <div class="flex items-center justify-between">
                    <ActionButton label="Save" type="submit" :disabled="saving" />
                    <ActionButton v-if="!create" label="Delete" variant="danger" @click="deleteScrapeType" :disabled="saving" />
                </div>
            </form>
        </div>
    </DefaultLayout>
</template>
