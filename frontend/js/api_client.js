// /frontend/js/api_client.js
// Secure Frontend Wrapper for JWT API Requests

const UrbanixAPI = {
    baseURL: '/api/index.php?route=',

    // Saves token from Auth response
    setToken(token) {
        localStorage.setItem('urbanix_jwt', token);
    },

    getToken() {
        return localStorage.getItem('urbanix_jwt');
    },

    clearToken() {
        localStorage.removeItem('urbanix_jwt');
    },

    // Core Fetch Wrapper
    async request(route, method = 'GET', body = null) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const config = { method, headers };
        if (body) config.body = JSON.stringify(body);

        try {
            const response = await fetch(this.baseURL + route, config);
            const data = await response.json();

            // Handle Global 401 Unauthorized (Expired Token)
            if (response.status === 401) {
                this.clearToken();
                window.showToast("Session expired. Re-authenticating.", "error");
                setTimeout(() => window.location.href = '?route=auth', 1500);
                return null;
            }

            if (!response.ok || data.status === 'error') {
                throw new Error(data.message || 'API Error');
            }

            return data;
        } catch (error) {
            console.error(`[API FAULT] ${route}:`, error);
            if(window.showToast) window.showToast(error.message, "error");
            throw error;
        }
    }
};

// Example Usage in your frontend:
// UrbanixAPI.request('wallet', 'POST', { action: 'game_win', amount: 5000 })
//   .then(res => console.log(res.data.new_balance));