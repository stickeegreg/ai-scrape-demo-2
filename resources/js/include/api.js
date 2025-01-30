import { ValidationError } from './validation-error.js';

async function parseResponse(response) {
    if (response.status === 422) {
        const validationData = await response.json();
        throw new ValidationError(validationData);
    }

    if (response.status >= 300) {
        throw new Error('Something went wrong');
    }

    return await response.json();
}

async function get(url) {
    const response = await fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    });

    return await parseResponse(response);
}

async function httpDelete(url) {
    const response = await fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        method: 'DELETE',
    });

    return await parseResponse(response);
}

async function post(url, data) {
    const response = await fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        method: 'POST',
        body: JSON.stringify(data),
    });

    return await parseResponse(response);
}

async function put(url, data) {
    const response = await fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        method: 'PUT',
        body: JSON.stringify(data),
    });

    return await parseResponse(response);
}

class Api {
    async listWebsites() {
        return await get('/api/websites');
    }
    async getWebsite(id) {
        return await get(`/api/websites/${id}`);
    }
    async deleteWebsite(id) {
        return await httpDelete(`/api/websites/${id}`);
    }
    async createWebsite(website) {
        return await post('/api/websites', website);
    }
    async updateWebsite(website) {
        return await put(`/api/websites/${website.id}`, website);
    }

    async listScrapeTypes() {
        return await get('/api/scrape-types');
    }
    async getScrapeType(id) {
        return await get(`/api/scrape-types/${id}`);
    }
    async deleteScrapeType(id) {
        return await httpDelete(`/api/scrape-types/${id}`);
    }
    async createScrapeType(website) {
        return await post('/api/scrape-types', website);
    }
    async updateScrapeType(website) {
        return await put(`/api/scrape-types/${website.id}`, website);
    }

    async listScrapes() {
        return await get('/api/scrapes');
    }
    async getScrape(id) {
        return await get(`/api/scrapes/${id}`);
    }
    async deleteScrape(id) {
        return await httpDelete(`/api/scrapes/${id}`);
    }
    async createScrape(scrape) {
        return await post('/api/scrapes', scrape);
    }
    async updateScrape(scrape) {
        return await put(`/api/scrapes/${scrape.id}`, scrape);
    }
    async runScrape(id) {
        return await post(`/api/scrapes/${id}/run`);
    }

    async listScrapeRuns() {
        return await get('/api/scrape-runs');
    }
    async getScrapeRun(id) {
        return await get(`/api/scrape-runs/${id}`);
    }
    async deleteScrapeRun(id) {
        return await httpDelete(`/api/scrape-runs/${id}`);
    }
    async createScrapeRun(scrapeId) {
        return await post('/api/scrape-runs', {scrape_id: scrapeId});
    }
    async updateScrapeRun(scrapeRun) {
        return await put(`/api/scrape-runs/${scrapeRun.id}`, scrapeRun);
    }
}

export const api = new Api();
