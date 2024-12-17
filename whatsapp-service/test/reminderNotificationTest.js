const axios = require('axios');
const { expect } = require('chai');
const WhatsAppService = require('../whatsappService');  // Corrected import path

describe('Reminder Notification Workflow', function() {
    let whatsappService;

    before(async function() {
        // Increase timeout for initialization
        this.timeout(60000);  // 1 minute timeout

        // Initialize WhatsApp service
        whatsappService = new WhatsAppService();
        
        // Add a promise to wait for connection
        await new Promise(async (resolve, reject) => {
            try {
                await whatsappService.initialize();

                // Listen for connection status
                whatsappService.sock.ev.on('connection.update', (update) => {
                    const { connection, qr } = update;
                    
                    if (qr) {
                        console.log('QR Code generated. Please scan to continue testing:');
                        console.log('IMPORTANT: Scan the QR code manually before proceeding');
                        reject(new Error('QR Code requires manual scanning'));
                    }

                    if (connection === 'open') {
                        console.log('WhatsApp connection established');
                        resolve();
                    }
                });
            } catch (error) {
                reject(error);
            }
        });
    });

    it('should send reminder notification via WhatsApp', async function() {
        // Increase timeout for async operations
        this.timeout(20000);

        // Test data
        const testUser = {
            name: 'Test User',
            phoneNumber: '081234567890',
            reminderId: 'TEST_REMINDER_001'
        };

        const reminderMessage = `Pengingat untuk ${testUser.name}\n` +
                                `ID Pengingat: ${testUser.reminderId}\n` +
                                `Waktu: ${new Date().toLocaleString()}`;

        try {
            // Attempt to send WhatsApp message
            const sendResult = await whatsappService.sendMessage(
                testUser.phoneNumber, 
                reminderMessage
            );

            // Assert message was sent successfully
            expect(sendResult).to.be.true;

            // Log successful test
            console.log('Reminder notification sent successfully');
        } catch (error) {
            console.error('Reminder notification test failed:', error);
            throw error;
        }
    });

    it('should check WhatsApp connection status', function() {
        const connectionStatus = whatsappService.getConnectionStatus();
        
        // Assert connection status
        expect(connectionStatus).to.be.an('object');
        expect(connectionStatus.status).to.equal('connected');
        expect(connectionStatus.connected).to.be.true;
    });

    after(async function() {
        // Optional cleanup
        if (whatsappService.sock) {
            await whatsappService.sock.logout();
        }
        console.log('Reminder notification tests completed');
    });
});
