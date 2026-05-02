// /frontend/js/api_client.js
// V8 Hardened JWT Fetch Wrapper - Subdomain Enforced

const UrbanixAPI = {
    getBaseURL() {
        // Enforce the strict api subdomain
        const apiDomain = window.URBANIX_CONFIG && window.URBANIX_CONFIG.apiUrl 
            ? window.URBANIX_CONFIG.apiUrl 
            : 'https://api.adurbanix.online';
        return apiDomain.replace(/\/$/, '') + '/index.php?route=';
    },

    setToken(token) { localStorage.setItem('urbanix_jwt', token); },
    getToken() { return localStorage.getItem('urbanix_jwt'); },
    clearToken() { localStorage.removeItem('urbanix_jwt'); },

    async request(route, method = 'GET', body = null) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        const token = this.getToken();
        if (token) headers['Authorization'] = `Bearer ${token}`;

        const config = { method, headers, credentials: 'omit' };
        if (body) config.body = JSON.stringify(body);

        try {
            const endpoint = this.getBaseURL() + route;
            const response = await fetch(endpoint, config);
            const text = await response.text();

            // Detect HTML response (Server errors/404s)
            if (text.trim().startsWith('<')) {
                console.error("Non-JSON detected:", text);
                throw new Error("System is returning HTML instead of data. Check Subdomain Document Root.");
            }

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error("Malformed JSON response from server.");
            }

            if (response.status === 401) {
                this.clearToken();
                if(window.showToast) window.showToast("Security Token Invalid.", "error");
                setTimeout(() => window.location.href = '?route=auth', 1000);
                return null;
            }

            if (!response.ok || data.status === 'error') {
                throw new Error(data.message || 'API Communication Fault');
            }

            return data;
        } catch (error) {
            console.error(`[API_CRASH] @${route}:`, error.message);
            throw error;
        }
    }
};