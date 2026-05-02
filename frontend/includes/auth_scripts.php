<?php
// /frontend/includes/auth_scripts.php
// Secure Javascript-driven Google Auth Initialization
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

    function handleCredentialResponse(response) {
        const payload = parseJwt(response.credential);
        const errorDiv = document.getElementById('auth-error');
        if(errorDiv) errorDiv.classList.add('hidden');

        fetch('<?= API_URL ?>/auth.php', {
            method: 'POST',
            credentials: 'include', // CRITICAL for subdomain sessions
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                google_id: payload.sub,
                email: payload.email,
                name: payload.name
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                const target = typeof redirectParam !== 'undefined' ? redirectParam : 'home';
                window.location.href = '?route=' + target;
            } else {
                if(errorDiv) {
                    errorDiv.innerText = "> " + data.message;
                    errorDiv.classList.remove('hidden');
                } else alert("Auth Error: " + data.message);
            }
        })
        .catch(err => {
            console.error("Auth Exception:", err);
            if(errorDiv) {
                errorDiv.innerText = "> Network/CORS Exception.";
                errorDiv.classList.remove('hidden');
            }
        });
    }

    // Dynamically initialize Google Auth once the script loads
    window.onload = function () {
        if(typeof window.URBANIX_CONFIG !== 'undefined' && window.URBANIX_CONFIG.googleClientId !== 'NOT_SET') {
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