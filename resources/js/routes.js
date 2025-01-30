import HomeView from './views/HomeView.vue';
import ScrapeJobsView from './views/ScrapeJobsView.vue';
import ScrapesView from './views/ScrapesView.vue';
import ScrapeTypesView from './views/ScrapeTypesView.vue';
import WebsitesView from './views/WebsitesView.vue';
import EditWebsiteView from './views/EditWebsiteView.vue';

export default [
    { path: '/', component: HomeView },
    { path: '/scrape-jobs', component: ScrapeJobsView },
    { path: '/scrapes', component: ScrapesView },
    { path: '/scrape-types', component: ScrapeTypesView },
    { path: '/websites', component: WebsitesView },
    { path: '/websites/create', component: EditWebsiteView, props: { create: true } },
    { path: '/websites/:id', component: EditWebsiteView },
];
