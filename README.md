
# Third-Party Enterprise Service Integration

This is a **Laravel-based application** designed to integrate with a **third-party enterprise service (ServiceNow)** for seamless data synchronization.  
The core functionalities include:  
- User authentication via **Google OAuth**  
- Ticket management in Laravel  
- Two-way data syncing between the local database and **ServiceNow**  

---

## Table of Contents
1. [Project Requirements & Setup](#project-requirements--setup)  
2. [User Authentication (Google OAuth)](#user-authentication-google-oauth)  
3. [Third-Party Integration (ServiceNow)](#third-party-integration-servicenow)  
4. [Testing & Verification](#testing--verification)  
5. [Time Estimate](#time-estimate)  

---

## 1. Project Requirements & Setup

This project was built using the following technologies. Please ensure they are installed on your system:

- PHP >= 8.1  
- Composer  
- MySQL Database  
- Node.js & NPM  
- A **publicly accessible server** (e.g., cPanel, AWS, DigitalOcean) is required for webhook testing  

### Setup Instructions

Clone the repository:

git clone https://github.com/moinulibr/laravel-ticket-sync-with-servicenow.git


Install dependencies:


composer install
npm install


Copy environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Configure your `.env` with DB and external service credentials.

Run migrations:

```bash
php artisan migrate
```

Start servers:

```bash
php artisan serve
npm run dev
```

---

## 2. User Authentication (Google OAuth)

This application uses **Google Sign-In** for authentication.

### Configuration Steps

1. Go to [Google Cloud Console](https://console.cloud.google.com/).
2. Create a new project and enable the **Google People API**.
3. Go to **Credentials** → Create **OAuth 2.0 Client ID** (Web Application).
4. Add your redirect URL:

   ```
   https://your-domain.com/auth/google/callback
   ```
5. Copy **Client ID** and **Client Secret** into `.env`:

   ```env
   GOOGLE_CLIENT_ID=your_client_id
   GOOGLE_CLIENT_SECRET=your_client_secret
   GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback
   ```

---

## 3. Third-Party Integration (ServiceNow)

The app integrates with a **ServiceNow Developer Instance** to synchronize ticket data.

### ServiceNow API Credentials

Add credentials to `.env`:

```env
SERVICE_NOW_URL=https://your-instance.service-now.com
SERVICE_NOW_USERNAME=your_username
SERVICE_NOW_PASSWORD=your_password
```

### Key Challenge: Webhook Limitations on Localhost

* Webhooks require **server-to-server communication**.
* Localhost (`127.0.0.1`) is not accessible from the internet.
* Tools like **ngrok** can be used but are unreliable (frequent URL changes).
* Therefore, this project was deployed to a **live cPanel server** to ensure stable webhook communication.

---

### ServiceNow Configuration

#### 1. REST Message Setup

* Create new **REST Message** named `filerskeepers webhook`.
* Endpoint URL:

  ```
  https://your-domain.com/api/servicenow/webhook
  ```
* Create a **POST Method** under the REST Message.

#### 2. Business Rule Setup

* Table: `incident`
* Condition: After **Update** where `State` changes
* Advanced → Add Script:

```javascript
(function executeRule(current, previous) {
    try {
        var r = new sn_ws.RESTMessageV2('filerskeepers webhook', 'post');
        r.setRequestHeader('Content-Type', 'application/json');
        r.setRequestBody(JSON.stringify({
            'ticket_number': current.number.toString(),
            'status': current.state.getDisplayValue(),
            'sys_id': current.sys_id.toString()
        }));

        var response = r.execute();
        var httpStatus = response.getStatusCode();
        gs.info("HTTP Status from Laravel: " + httpStatus);
    } catch (ex) {
        gs.log("Error in Business Rule: " + ex.getMessage());
    }
})(current, previous);
```

---

## 4. Testing & Verification

1. **Login** → Log in via Google Sign-In.
2. **Create Ticket** → Add new ticket from dashboard.
3. **Sync** → Click **Sync** (or **Sync All**) to push to ServiceNow.
4. **Verify on ServiceNow** → Check new **Incident** in *All Incidents*.
5. **Webhook Sync** → Change `State` in ServiceNow → Laravel updates automatically.

---

## 5. Time Estimate

| Task                      | Hours       |
| ------------------------- | ----------- |
| Initial Setup & Auth      | \~3 hrs     |
| Data Model & UI           | \~3 hrs     |
| API Integration & Webhook | \~8 hrs     |
| Refactoring & Testing     | \~2 hrs     |
| **Total**                 | \~15–20 hrs |

---
