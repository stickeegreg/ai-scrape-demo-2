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
        return await get(`/api/websites/${id}`);
    }
    async createWebsite(website) {
        return await post('/api/websites', website);
    }
    async updateWebsite(website) {
        return await put(`/api/websites/${website.id}`, website);
    }
}

export const api = new Api();
