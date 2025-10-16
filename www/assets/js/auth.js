/**
 * Utilidades de autenticación para el sistema ELECTROTEC
 */

const Auth = {
    /**
     * Verifica si el usuario está autenticado
     * @returns {Object|null} Datos del usuario o null si no está autenticado
     */
    getUser() {
        const token = localStorage.getItem('token');
        const userStr = localStorage.getItem('user');
        
        if (!token || !userStr) {
            return null;
        }
        
        try {
            return JSON.parse(userStr);
        } catch (e) {
            return null;
        }
    },

    /**
     * Obtiene el token JWT
     * @returns {string|null}
     */
    getToken() {
        return localStorage.getItem('token');
    },

    /**
     * Verifica autenticación y redirige si no está autenticado
     * @param {string} requiredRole - 'admin' o 'cliente' (opcional)
     * @returns {Object} Datos del usuario autenticado
     */
    requireAuth(requiredRole = null) {
        const user = this.getUser();
        
        if (!user) {
            window.location.href = 'login.php';
            throw new Error('No autenticado');
        }
        
        if (requiredRole && user.tipo !== requiredRole) {
            // Redirigir a la página apropiada según el rol
            if (user.tipo === 'admin') {
                window.location.href = 'dashboard.php';
            } else {
                window.location.href = 'cliente.php';
            }
            throw new Error('Acceso no autorizado');
        }
        
        return user;
    },

    /**
     * Cierra sesión
     */
    logout() {
        localStorage.clear();
        window.location.href = 'login.php';
    },

    /**
     * Realiza una petición fetch con autenticación
     * @param {string} url - URL de la API
     * @param {Object} options - Opciones de fetch (method, body, etc.)
     * @returns {Promise<any>} Respuesta JSON
     */
    async fetchWithAuth(url, options = {}) {
        const token = this.getToken();
        
        const headers = {
            'Accept': 'application/json',
            ...(options.headers || {})
        };
        
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        const response = await fetch(url, {
            ...options,
            headers
        });
        
        let payload = null;
        try {
            payload = await response.json();
        } catch (e) {
            // Si no es JSON, continuar
        }
        
        // Si es 401, el token expiró o es inválido
        if (response.status === 401) {
            localStorage.clear();
            window.location.href = 'login.php';
            throw new Error('Sesión expirada');
        }
        
        if (!response.ok) {
            let msg = payload?.message || `HTTP ${response.status}`;
            if (payload?.details?.error) {
                msg += ` — ${payload.details.error}`;
            }
            throw new Error(msg);
        }
        
        return payload;
    }
};

// Hacer disponible globalmente
window.Auth = Auth;
