module.exports = {
    // Konfigurasi WhatsApp Service
    whatsapp: {
        // URL untuk service WhatsApp
        apiUrl: process.env.WHATSAPP_API_URL || 'http://localhost:3000',
        
        // Kunci API untuk otentikasi
        apiKey: process.env.WHATSAPP_API_KEY || 'test-key-default',
        
        // Mode debug
        debugMode: process.env.DEBUG_MODE === 'true' || false,
        
        // Timeout untuk koneksi
        timeout: {
            connection: parseInt(process.env.WHATSAPP_CONNECTION_TIMEOUT) || 30000,
            request: parseInt(process.env.WHATSAPP_REQUEST_TIMEOUT) || 20000
        },
        
        // Konfigurasi logging
        logging: {
            level: process.env.LOG_LEVEL || 'info',
            enabled: process.env.LOGGING_ENABLED === 'true' || true
        }
    }
};
