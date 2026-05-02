<?php
// /frontend/includes/auth_scripts.php
// Secure Javascript-driven Google Auth Initialization - JWT Edition
?>
<script>
    function parseJwt(token) {
        const base64Url = token.split('.')[1];
        const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
        return JSON.parse(jsonPayload);
    }

    async function handleCredentialResponse(response) {
        const payload = parseJwt(response.credential);
        const errorDiv = document.getElementById('auth-error');
        if(errorDiv) errorDiv.classList.add('hidden');

        try {
            if (typeof UrbanixAPI === 'undefined') throw new Error("API Client not loaded.");

            // Use the centralized UrbanixAPI for Google Login
            const res = await UrbanixAPI.request('auth', 'POST', {
                action: 'google_login', // Send specific action
                google_id: payload.sub,
                email: payload.email,
                name: payload.name
            });

            // Store the JWT Securely
            if(res.data && res.data.token) {
                UrbanixAPI.setToken(res.data.token);
            }
            
            const target = typeof redirectParam !== 'undefined' ? redirectParam : 'home';
            window.location.href = '?route=' + target;

        } catch (err) {
            console.error("Auth Exception:", err);
            if(errorDiv) {
                errorDiv.innerText = "> Google Sync Failed: " + err.message;
                errorDiv.classList.remove('hidden');
            } else {
                alert("Auth Error: " + err.message);
            }
        }
    }

    // Dynamically initialize Google Auth once the script loads
    window.onload = function () {
        if(typeof window.URBANIX_CONFIG !== 'undefined' && window.URBANIX_CONFIG.googleClientId && window.URBANIX_CONFIG.googleClientId !== 'NOT_SET') {
            google.accounts.id.initialize({
                client_id: window.URBANIX_CONFIG.googleClientId,
                callback: handleCredentialResponse,
                context: "use",
                ux_mode: "popup"
            });
            
            // Render the button inside a specific container
            const buttonContainer = document.getElementById('google-btn-container');
            if(buttonContainer) {
                google.accounts.id.renderButton(
                    buttonContainer,
                    { theme: "filled_black", size: "large", width: 300, shape: "pill" }
                );
            }
        }
    };
</script>