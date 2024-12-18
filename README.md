<p align="center">
  <img src="https://github.com/arafat-web/Task-Manager/assets/26932301/d5f6a26e-32d1-44dc-aee5-9548a44506ae" alt="Icon Description">
</p>

<h1 align="center">Task Manager</h1>

<p align="center">
  <img src="https://img.shields.io/github/stars/arafat-web/Task-Manager?style=for-the-badge" alt="Total Issues">
  <img src="https://img.shields.io/github/issues/arafat-web/Task-Manager?style=for-the-badge">
  <img src="https://img.shields.io/github/license/arafat-web/Task-Manager?style=for-the-badge" alt="License">
</p>

## Introduction
Task Manager is an open-source Laravel application designed to simplify the process of managing project alone with task. The task page is designed like clickup or trello board, so developer will get a very flexbility to handle all This documentation provides a step-by-step guide on how to set up the project.

### Prerequisites
- PHP 8.1 or higher
- Composer
- Laravel 10 or higher
- MySQL or any other supported database system

## Setup Instructions

### Step 1: Clone the Repository
```
git clone https://github.com/arafat-web/Task-Manager.git
cd Task-Manager
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Configure Environment Variables
Duplicate the `.env.example` file and rename it to `.env`. Update the following variables:


### Step 4: Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Run Migrations and Seed Database
```bash
php artisan migrate --seed
```

### Step 6: Serve the Application
```bash
php artisan serve
```

Access the application in your browser at `http://localhost:8000`.


## How to Use

### 1. Task Management
Task Manager allows users to efficiently manage projects and tasks through a user-friendly interface similar to ClickUp or Trello. Here are the main features:

1. **Login to the admin panel:**
    ```
    Email: admin@example.com
    Password: secret
    ```

2. **Projects:**
   - Create and manage multiple projects.
   - Assign tasks to specific projects to keep everything organized.

3. **Tasks:**
   - Add, edit, and delete tasks within a project.
   - Use drag-and-drop functionality to move tasks between different stages or statuses.

4. **Notes:**
   - Attach notes to tasks or projects for additional details and context.
   - Keep track of important information that doesn't fit into tasks.

5. **Reminders:**
   - Set reminders for tasks to ensure deadlines are met.

6. **Routines:**
   - Define routine tasks that need to be done regularly.

7. **Files:**
   - Upload and attach files to tasks or projects.
   - Easily access all necessary documents and resources in one place.

## WhatsApp Reminders Integration

### Prerequisites
- PHP 8.1 or higher
- Windows OS (for Task Scheduler)
- WhatsApp Web active and logged in
- XAMPP or similar web server

### Setup WhatsApp Integration

1. **Install WhatsApp Service**
   ```bash
   cd whatsapp-service
   npm install
   ```

2. **Configure WhatsApp Service**
   - Start the WhatsApp service:
     ```bash
     node server.js
     ```
   - Scan QR code with WhatsApp mobile app when prompted
   - Keep the service running for WhatsApp functionality

3. **Configure Laravel Environment**
   Update your `.env` file with WhatsApp service settings:
   ```
   WHATSAPP_SERVICE_URL=http://localhost:3000
   ```

### Automated Reminder Setup

1. **Create Batch File**
   Create `run_scheduler.bat` in project root with following content:
   ```batch
   @echo off
   cd /d "C:\xampp-pa\htdocs\manajemen-proyek"
   "C:\xampp-pa\php\php.exe" artisan reminders:send --force
   if %ERRORLEVEL% EQU 0 (
       echo Command completed successfully
   ) else (
       echo Command failed with error code %ERRORLEVEL%
   )
   exit /b 0
   ```

2. **Configure Windows Task Scheduler**
   - Open Task Scheduler (taskschd.msc)
   - Create new task with these settings:
     - **General tab:**
       - Name: `SendReminders`
       - Run with highest privileges: Yes
       - Use SYSTEM account
     - **Triggers tab:**
       - New Trigger: Run every 1 minute
     - **Actions tab:**
       - Program: Path to run_scheduler.bat
       - Start in: Project root directory
     - **Settings tab:**
       - Stop task if runs longer than: 30 seconds
       - If task is already running: Stop the existing instance

3. **Verify Setup**
   - Create a test reminder in the application
   - Wait for the scheduled time
   - Check WhatsApp for the reminder message
   - Verify reminder status in application dashboard

### Troubleshooting

1. **WhatsApp Service Issues**
   - Ensure WhatsApp Web is logged in
   - Check if WhatsApp service is running (port 3000)
   - Verify QR code has been scanned

2. **Task Scheduler Issues**
   - Check Task Scheduler History for errors
   - Verify batch file path is correct
   - Ensure PHP path in batch file is correct

3. **Reminder Not Sending**
   - Check application logs in `storage/logs`
   - Verify reminder date and time are correct
   - Ensure WhatsApp recipient number is valid

### Important Notes

- Keep the WhatsApp service running continuously
- Server must remain active for reminders to work
- Regular monitoring of Task Scheduler recommended
- Backup WhatsApp session to prevent re-authentication
- Test system after server restarts

## Demo
<img src="https://github.com/arafat-web/Task-Manager/assets/26932301/d5f6a26e-32d1-44dc-aee5-9548a44506ae" alt="Demo">
<img src="https://github.com/arafat-web/Task-Manager/assets/26932301/8795129a-69e5-4911-bb26-caae3bca50be" alt="Demo">
<img src="https://github.com/arafat-web/Task-Manager/assets/26932301/bd96fa3c-7f43-4ab7-8aa1-4614629d9d26" alt="Demo">

## Contributing
For any issues or inquiries, please open an issue on the [Issues](https://github.com/arafat-web/Task-Manager/issues).<br/>
If you can help me by contributing. Please don't hesitate to open a [Pull Request](https://github.com/arafat-web/Task-Manager/pulls).<br/>
🎉 **Thanks for reading!** 🌟  



### Contact Me
[![Email](https://img.shields.io/badge/Gmail-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:arafat.122260@gmail.com)
[![Facbook](https://img.shields.io/badge/Facebook-1877F2?style=for-the-badge&logo=facebook&logoColor=white)](https://www.facebook.com/arafathossain000)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/arafat-hossain-ar-a174b51a6/)
[![Sololearn](https://img.shields.io/badge/-Sololearn-3a464b?style=for-the-badge&logo=Sololearn&logoColor=white)](https://www.sololearn.com/profile/4703319)
[![Website](https://img.shields.io/badge/website-000000?style=for-the-badge&logo=About.me&logoColor=white)](https://arafatdev.com)
