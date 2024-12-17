const express = require('express');
const cors = require('cors');
const WhatsAppService = require('./whatsappService');
const config = require('./config');
const bodyParser = require('body-parser');
const winston = require('winston');

const app = express();
const port = process.env.PORT || 3000;

// Create WhatsApp service instance
const whatsappService = new WhatsAppService();

// Konfigurasi logging
const logger = winston.createLogger({
    level: config.whatsapp.logging.level,
    format: winston.format.combine(
        winston.format.timestamp(),
        winston.format.json()
    ),
    transports: [
        new winston.transports.Console(),
        new winston.transports.File({ filename: 'whatsapp-service.log' })
    ]
});

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Initialize WhatsApp service
whatsappService.initialize().catch(err => {
    logger.error('Failed to initialize WhatsApp service', { error: err });
});

// Endpoint status
app.get('/status', (req, res) => {
    const status = whatsappService.getConnectionStatus();
    logger.info('Status check', { status });
    res.json(status);
});

// Endpoint health check
app.get('/health', (req, res) => {
    const status = {
        status: 'ok',
        timestamp: new Date().toISOString(),
        whatsappConnected: whatsappService.sock !== null
    };
    logger.info('Health check', { status });
    res.json(status);
});

// Endpoint untuk mengirim pesan
app.post('/send-message', async (req, res) => {
    try {
        const { phone, message } = req.body;

        // Validasi input
        if (!phone || !message) {
            logger.error('Invalid send message request', { 
                phone: phone ? 'provided' : 'missing', 
                message: message ? 'provided' : 'missing' 
            });
            return res.status(400).json({ 
                success: false, 
                error: 'Phone number and message are required' 
            });
        }

        // Log incoming message request
        logger.info('Received send message request', { 
            phone, 
            messageLength: message.length 
        });

        // Kirim pesan
        const sendResult = await whatsappService.sendMessage(phone, message);

        if (sendResult) {
            logger.info('Message sent successfully', { 
                recipient: phone, 
                messageLength: message.length 
            });
            return res.status(200).json({ 
                success: true, 
                message: 'Message sent successfully' 
            });
        } else {
            logger.error('Failed to send message', { 
                recipient: phone, 
                messageLength: message.length 
            });
            return res.status(500).json({ 
                success: false, 
                error: 'Failed to send message' 
            });
        }
    } catch (error) {
        // Tangani kesalahan yang tidak terduga
        logger.error('Unexpected error in send message endpoint', {
            errorMessage: error.message,
            errorStack: error.stack
        });
        res.status(500).json({ 
            success: false, 
            error: 'Internal server error' 
        });
    }
});

// Endpoint untuk mengirim reminder
app.post('/send-reminder', async (req, res) => {
    try {
        const { phone, message } = req.body;
        
        if (!phone || !message) {
            return res.status(400).json({ error: 'Phone and message are required' });
        }

        const normalizedPhone = normalizePhoneNumber(phone);
        
        const result = await whatsappService.sendMessage(normalizedPhone, message);
        
        if (result) {
            res.json({ success: true, message: 'Reminder sent successfully' });
        } else {
            res.status(500).json({ error: 'Failed to send reminder' });
        }
    } catch (error) {
        logger.error('Error sending reminder:', error);
        res.status(500).json({ error: 'Internal server error' });
    }
});

// Fungsi normalisasi nomor telepon
function normalizePhoneNumber(phone) {
    // Hapus semua karakter non-digit
    phone = phone.replace(/\D/g, '');
    
    // Tambahkan prefix 62 jika dimulai dengan 0
    if (phone.startsWith('0')) {
        phone = '62' + phone.slice(1);
    }
    
    // Tambahkan prefix 62 jika tidak dimulai dengan 62
    if (!phone.startsWith('62')) {
        phone = '62' + phone;
    }
    
    return phone;
}

// Start server
app.listen(port, () => {
    logger.info(`WhatsApp service listening on port ${port}`);
});

// Penanganan shutdown yang lebih baik
process.on('SIGTERM', () => {
    logger.info('SIGTERM received. Shutting down gracefully');
    app.close(() => {
        logger.info('Process terminated');
        process.exit(0);
    });
});

module.exports = app;
