// /frontend/js/api_client.js
// Dynamic JWT Fetch Wrapper with Failsafe Subdomain Routing & HTML Interception

const UrbanixAPI = {
    getBaseURL() {
        // STRICT FAILSAFE: Force the api subdomain if ENV injection fails
        const fallbackApiUrl = 'https://api.adurbanix.online';
        const apiDomain = (window.URBANIX_CONFIG && window.URBANIX_CONFIG.apiUrl) 
            ? window.URBANIX_CONFIG.apiUrl 
            : fallbackApiUrl;
            
        return apiDomain + '/index.php?route=';
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

        const config = { method, headers };
        if (body) config.body = JSON.stringify(body);

        try {
            const endpoint = this.getBaseURL() + route;
            const response = await fetch(endpoint, config);
            
            // SECURITY CATCH: Prevent "Unexpected token '<'" JSON crash
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("API Gateway returned HTML. Verify aaPanel Subdomain Document Root points to /api.");
            }

            const data = await response.json();

            // Handle Global 401 Unauthorized
            if (response.status === 401) {
                this.clearToken();
                if(window.showToast) window.showToast("Session expired. Re-authenticating.", "error");
                setTimeout(() => window.location.href = '?route=auth', 1500);
                return null;
            }

            if (!response.ok || data.status === 'error') {
                throw new Error(data.message || 'API Communication Error');
            }

            return data;
        } catch (error) {
            console.error(`[API FAULT] ${route}:`, error);
            throw error;
        }
    }
};