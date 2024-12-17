const { default: makeWASocket, DisconnectReason, useMultiFileAuthState } = require('@whiskeysockets/baileys');
const qrcode = require('qrcode-terminal');

class WhatsAppService {
    constructor() {
        this.sock = null;
        this.qrGenerated = false;
        console.log('WhatsApp service initialized');
    }

    async initialize() {
        console.log('Initializing WhatsApp connection...');
        const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');
        
        this.sock = makeWASocket({
            printQRInTerminal: true,
            auth: state,
        });

        this.sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;
            console.log('Connection status:', connection);

            if (qr) {
                console.log('New QR Code received, please scan:');
                qrcode.generate(qr, { small: true });
            }

            if (connection === 'close') {
                const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
                console.log('Connection closed due to:', lastDisconnect?.error?.message);
                if (shouldReconnect) {
                    console.log('Attempting to reconnect...');
                    await this.initialize();
                }
            } else if (connection === 'open') {
                console.log('WhatsApp connection established successfully!');
            }
        });

        this.sock.ev.on('creds.update', saveCreds);
    }

    async sendMessage(phoneNumber, message) {
        if (!this.sock) {
            console.error('WhatsApp client not initialized');
            throw new Error('WhatsApp client not initialized');
        }

        try {
            // Format phone number to WhatsApp format
            const formattedNumber = phoneNumber.replace(/\D/g, '') + '@s.whatsapp.net';
            console.log('Attempting to send message to:', formattedNumber);
            
            const result = await this.sock.sendMessage(formattedNumber, {
                text: message
            });
            
            console.log('Message sent successfully:', result);
            return true;
        } catch (error) {
            console.error('Error sending message:', error);
            return false;
        }
    }
}

module.exports = new WhatsAppService();
