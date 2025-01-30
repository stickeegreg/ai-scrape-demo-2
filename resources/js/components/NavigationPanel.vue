<script setup>
import LinkTo from '../components/LinkTo.vue';
</script>

<template>
    <nav class="bg-white shadow-md rounded px-8 pt-6 pb-8 mr-4">
        <LinkTo class="block" to="/">Dashboard</LinkTo>
        <LinkTo class="block" to="/websites">Websites</LinkTo>
        <LinkTo class="block" to="/scrape-types">Scrape Types</LinkTo>
        <LinkTo class="block" to="/scrapes">Scrapes</LinkTo>
        <LinkTo class="block" to="/scrape-runs">Scrape Runs</LinkTo>
    </nav>
</template>
