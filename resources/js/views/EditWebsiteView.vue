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
const website = ref(null);
const saving = ref(false);

watch(
  () => route.params.id,
  (newId, oldId) => {
    id.value = newId;

    loadWebsite();
  }
);

const loadWebsite = async () => {
    loading.value = true;
    error.value = null;
    website.value = null;

    try {
        website.value = await api.getWebsite(id.value);
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
        website.value = {
            name: '',
            url: '',
            prompt: '',
        };
    } else {
        loadWebsite();
    }
});

const deleteWebsite = async () => {
    if (!confirm(`Are you sure you want to delete the website "${website.value.name}"?`)) {
        return;
    }

    try {
        await api.deleteWebsite(website.value.id);
        websites.value = websites.value.filter((w) => w.id !== website.id);
    } catch (error) {
        console.error(error);
        error.value = error.message;
        alert(`Failed to delete the website "${website.value.name}".`);
    }
};

const saveWebsite = async () => {
    error.value = null;
    errors.value = null;
    saving.value = true;

    try {
        if (props.create) {
            const newWebsite = await api.createWebsite(website.value);
            router.push(`/websites/${newWebsite.id}`);
        } else {
            await api.updateWebsite(website.value);
        }

        alert('Website saved.');
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
        <h1 class="mb-4 text-4xl font-bold text-gray-900">{{ create ? 'Create' : 'Edit' }} Website</h1>
        {{ error }}

        <div v-if="loading">Loading...</div>
        <div v-else>
            <form
                class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4"
                @submit.prevent="saveWebsite"
            >
                <TextInput
                    name="name"
                    label="Name"
                    placeholder="Name"
                    v-model="website.name"
                    :error="errors?.name?.[0]"
                    required
                />
                <TextInput
                    name="url"
                    label="URL"
                    placeholder="https://example.com"
                    v-model="website.url"
                    :error="errors?.url?.[0]"
                    required
                />
                <TextInput
                    name="prompt"
                    label="Custom Prompt"
                    placeholder="Custom prompt"
                    v-model="website.prompt"
                    :error="errors?.prompt?.[0]"
                    type="textarea"
                />
                <div class="flex items-center justify-between">
                    <ActionButton label="Save" type="submit" :disabled="saving" />
                    <ActionButton v-if="!create" label="Delete" variant="danger" @click="deleteWebsite" :disabled="saving" />
                </div>
            </form>
        </div>
    </DefaultLayout>
</template>
