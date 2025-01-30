import HomeView from './views/HomeView.vue';
import ScrapeRunsView from './views/ScrapeRunsView.vue';
import ScrapesView from './views/ScrapesView.vue';
import EditScrapeView from './views/EditScrapeView.vue';
import ScrapeTypesView from './views/ScrapeTypesView.vue';
import EditScrapeTypeView from './views/EditScrapeTypeView.vue';
import WebsitesView from './views/WebsitesView.vue';
import EditWebsiteView from './views/EditWebsiteView.vue';

export default [
    { path: '/', component: HomeView },
    { path: '/scrape-runs', component: ScrapeRunsView },
    { path: '/scrapes', component: ScrapesView },
    { path: '/scrapes/create', component: EditScrapeView, props: { create: true } },
    { path: '/scrapes/:id', component: EditScrapeView },
    { path: '/scrape-types', component: ScrapeTypesView },
    { path: '/scrape-types/create', component: EditScrapeTypeView, props: { create: true } },
    { path: '/scrape-types/:id', component: EditScrapeTypeView },
    { path: '/websites', component: WebsitesView },
    { path: '/websites/create', component: EditWebsiteView, props: { create: true } },
    { path: '/websites/:id', component: EditWebsiteView },
];
