const { default: makeWASocket, DisconnectReason, useMultiFileAuthState } = require('@whiskeysockets/baileys');
const qrcode = require('qrcode-terminal');
const fs = require('fs').promises;
const path = require('path');

class WhatsAppService {
    constructor() {
        this.sock = null;
        this.qrGenerated = false;
        this.connectionStatus = 'disconnected';
        this.authPath = path.join(__dirname, 'auth_info_baileys');
        console.log('WhatsApp service initialized');
    }

    async initialize() {
        console.log('Initializing WhatsApp connection...');
        
        try {
            // Ensure auth directory exists
            await fs.mkdir(this.authPath, { recursive: true });

            const { state, saveCreds } = await useMultiFileAuthState(this.authPath);
            
            this.sock = makeWASocket({
                printQRInTerminal: true,
                auth: state,
                // Add more configuration for stability
                syncFullHistory: false,
                markOnlineOnConnect: false,
                generateHighQualityLinkPreview: false
            });

            // Connection event handling
            this.sock.ev.on('connection.update', async (update) => {
                const { connection, lastDisconnect, qr } = update;
                console.log('Connection update:', connection);

                if (qr) {
                    console.log('New QR Code received, please scan:');
                    qrcode.generate(qr, { small: true });
                    this.qrGenerated = true;
                    this.connectionStatus = 'qr_generated';
                }

                if (connection === 'close') {
                    const shouldReconnect = 
                        lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
                    
                    console.log('Connection closed due to:', 
                        lastDisconnect?.error?.message || 'Unknown reason');
                    
                    this.connectionStatus = 'disconnected';
                    
                    if (shouldReconnect) {
                        console.log('Attempting to reconnect...');
                        await this.initialize();
                    }
                } else if (connection === 'open') {
                    console.log('WhatsApp connection established successfully!');
                    this.connectionStatus = 'connected';
                    this.qrGenerated = false;
                }
            });

            // Credentials update event
            this.sock.ev.on('creds.update', saveCreds);

        } catch (error) {
            console.error('WhatsApp initialization error:', error);
            this.connectionStatus = 'error';
            throw error;
        }
    }

    async sendMessage(phoneNumber, message) {
        if (!this.sock) {
            console.error('WhatsApp client not initialized');
            throw new Error('WhatsApp client not initialized');
        }

        if (this.connectionStatus !== 'connected') {
            console.error('WhatsApp not connected');
            throw new Error('WhatsApp not connected');
        }

        // Pastikan nomor telepon dalam format yang benar
        const formattedNumber = this.formatPhoneNumber(phoneNumber);

        try {
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

    // Metode untuk memformat nomor telepon ke format WhatsApp
    formatPhoneNumber(phone) {
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
        
        // Tambahkan @s.whatsapp.net
        return phone + '@s.whatsapp.net';
    }

    // Metode untuk mendapatkan status koneksi
    getConnectionStatus() {
        return {
            status: this.connectionStatus,
            qrGenerated: this.qrGenerated,
            connected: this.sock !== null && this.connectionStatus === 'connected'
        };
    }

    // Metode untuk logout
    async logout() {
        if (this.sock) {
            try {
                await this.sock.logout();
            } catch (error) {
                console.error('Logout error:', error);
            }
        }
    }
}

module.exports = WhatsAppService;
