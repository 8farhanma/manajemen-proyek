# Reminder Notification Test Workflow

## Overview
This test suite verifies the complete workflow of sending reminder notifications via WhatsApp, from database creation to message delivery.

## Test Scenarios
1. **Reminder Creation Test**
   - Creates a test user
   - Generates a test reminder
   - Verifies reminder is saved in the database

2. **WhatsApp Notification Test**
   - Checks WhatsApp service connection
   - Sends a test reminder notification
   - Validates message delivery

## Prerequisites
- WhatsApp service must be running
- Laravel backend must be accessible
- Node.js dependencies installed

## Running Tests
```bash
# Install dependencies
npm install

# Run tests
npm test

# Watch mode (for development)
npm run test:watch
```

## Test Configuration
- Test user phone number: `081234567890`
- Reminder test data generated dynamically
- Logs saved in application log files

## Troubleshooting
- Ensure WhatsApp service is connected before running tests
- Check network connectivity
- Verify phone number formatting
