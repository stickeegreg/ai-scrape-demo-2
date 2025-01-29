import HomeView from './views/HomeView.vue';
import ScrapeJobsView from './views/ScrapeJobsView.vue';
import ScrapesView from './views/ScrapesView.vue';
import ScrapeTypesView from './views/ScrapeTypesView.vue';
import WebsitesView from './views/WebsitesView.vue';

export default [
    { path: '/', component: HomeView },
    { path: '/scrape-jobs', component: ScrapeJobsView },
    { path: '/scrapes', component: ScrapesView },
    { path: '/scrape-types', component: ScrapeTypesView },
    { path: '/websites', component: WebsitesView },
];
