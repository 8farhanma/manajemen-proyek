const express = require('express');
const whatsappService = require('./whatsappService');

const app = express();
app.use(express.json());

// Initialize WhatsApp service when server starts
console.log('Starting WhatsApp service...');
whatsappService.initialize().catch(console.error);

// Health check endpoint
app.get('/health', (req, res) => {
    const status = {
        status: 'ok',
        timestamp: new Date().toISOString(),
        whatsappConnected: whatsappService.sock !== null
    };
    console.log('Health check:', status);
    res.json(status);
});

// Endpoint to send reminder
app.post('/send-reminder', async (req, res) => {
    console.log('Received reminder request:', req.body);
    const { phoneNumber, message } = req.body;

    if (!phoneNumber || !message) {
        console.error('Missing required fields');
        return res.status(400).json({ error: 'Phone number and message are required' });
    }

    try {
        const success = await whatsappService.sendMessage(phoneNumber, message);
        if (success) {
            console.log('Reminder sent successfully to:', phoneNumber);
            res.json({ status: 'success', message: 'Reminder sent successfully' });
        } else {
            console.error('Failed to send reminder');
            res.status(500).json({ status: 'error', message: 'Failed to send reminder' });
        }
    } catch (error) {
        console.error('Error sending reminder:', error);
        res.status(500).json({ status: 'error', message: error.message });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`WhatsApp service listening on port ${PORT}`);
});
