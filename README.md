# Davao Sugar Central Co., Inc. - ERP System

## System Title
**ERP WITH FERTILIZER DISTRIBUTION AUTOMATION MODULE AND SUGARCANE FARMERS ANALYTICS**

## Company Profile
- **Legal Name:** Davao Sugar Central Co., Inc.
- **Established:** 1970
- **Parent Company:** Pacific Sugar Holdings Corp. (PSHC) / Filinvest Development Corporation (FDC)
- **Head Plant / Mill Location:** Salutillo St., Barangay Guihing, Hagonoy, Davao del Sur, Philippines
- **Primary Outputs:** Raw sugar, Refined sugar, Molasses, Bagasse, Other sugarcane by-products

## Technology Stack
- **Framework:** Laravel (PHP)
- **Database:** SQLite (`database/database.sqlite`)
- **Frontend Engine:** Laravel Blade Templates
- **Styling:** Tailwind CSS
- **Charts & Visualizations:** Chart.js
- **ORM & Auth:** Laravel Eloquent ORM & Standard Laravel Authentication
- **Version Control:** Git & GitHub

---

## Setup & Local Installation

### Prerequisites
- PHP 8.3+ or PHP 8.4+ with SQLite extension enabled (`php-sqlite3` or `pdo_sqlite`)
- Composer
- Node.js & npm (for bundling Tailwind CSS assets)

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd platform-dev
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Configure Environment File**
   If `.env` does not exist, copy `.env.example`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Ensure `.env` contains:
   ```env
   DB_CONNECTION=sqlite
   ```

4. **Create & Initialize SQLite Database**
   In your terminal or PowerShell, ensure the SQLite database file exists:
   - **Windows PowerShell:**
     ```powershell
     if (!(Test-Path database/database.sqlite)) { New-Item database/database.sqlite -ItemType File }
     ```
   - **Linux / macOS:**
     ```bash
     touch database/database.sqlite
     ```

5. **Run Migrations & Seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Install & Build Frontend Assets (Tailwind CSS)**
   ```bash
   npm install
   npm run build
   ```

7. **Start the Laravel Development Server**
   ```bash
   php artisan serve
   ```
   Access the web application at `http://127.0.0.1:8000`.

---

## Default Access Credentials
Once seeded, use the following logins to test different roles:
- **System Administrator:** `admin@davaosugar.com` / `password`
- **Warehouse Staff:** `warehouse@davaosugar.com` / `password`
- **HR / Employee:** `hr@davaosugar.com` / `password`
- **Sales Staff:** `sales@davaosugar.com` / `password`
- **Farmer:** `farmer@davaosugar.com` / `password`
- **Finance:** `finance@davaosugar.com` / `password`
- **Management:** `management@davaosugar.com` / `password`

---

## System Architecture & Features
1. **Farmer Management:** Digital profiling, farm locations, crop year production, and activity logs.
2. **Fertilizer Distribution Automation Module:** Request approval workflow with auto-inventory deduction and stock validations.
3. **Inventory Management:** Stock-in/out, reorder level alerts, and transaction logs.
4. **HR & Payroll:** Employee registry, department management, and automated payslip generation.
5. **Sales & Trade Management:** Customer sales orders, import/export monitoring.
6. **Sugarcane Farmers & Operational Analytics:** Descriptive analytics and predictive trends (sales, inventory demand, fertilizer demand).
7. **Reports & Audit Logs:** Comprehensive reporting and audit trail logging.
