# Local Development Setup Guide

Professional setup guide for running the Real-Time Live Poll Platform locally on Windows, Mac, or Linux.

## System Requirements

- PHP 8.0 or higher with PDO MySQL extension
- MySQL 5.7+ or MariaDB 10.3+
- Apache 2.4+ (with mod_rewrite enabled) or Nginx
- Modern web browser

## Quick Setup (Windows with XAMPP)

### Step 1: Install XAMPP

1. Download XAMPP from: https://www.apachefriends.org/
2. Run the installer and follow the setup wizard
3. Choose installation path (default: C:\xampp)
4. Install Apache and MySQL components
5. Complete the installation

### Step 2: Start Services

1. Open XAMPP Control Panel (xampp-control.exe)
2. Click "Start" for Apache
3. Click "Start" for MySQL
4. Wait for both services to show "Running"

### Step 3: Create Database

#### Method A: Using phpMyAdmin (Recommended for GUI)

1. Open browser: http://localhost/phpmyadmin
2. Click "New" or use the SQL tab
3. Copy and paste the contents of `database/schema.sql`
4. Click "Go" to execute all SQL statements
5. Verify tables are created in the "polling_system" database

#### Method B: Using MySQL Command Line

1. Open Command Prompt
2. Navigate to the project: `cd "C:\Users\sarve\Desktop\Secure-transport\Mohit"`
3. Run: `"C:\xampp\mysql\bin\mysql" -u root < database/schema.sql`

### Step 4: Copy Project to Web Root

1. Copy the entire `Mohit` folder to: `C:\xampp\htdocs\`
2. Path should be: `C:\xampp\htdocs\Mohit`

### Step 5: Access Application

1. Open your browser
2. Navigate to: **http://localhost/Mohit/public/**
3. Login with test credentials:
   - Email: admin@polling.test
   - Password: admin123

## Setup (macOS with XAMPP)

### Step 1: Install XAMPP

1. Download XAMPP for macOS: https://www.apachefriends.org/
2. Open the .dmg file and drag XAMPP to Applications
3. Launch XAMPP Application Manager from Applications/XAMPP

### Step 2: Start Services

1. Open XAMPP Application Manager
2. Click "Start" for Apache
3. Click "Start" for MySQL
4. Wait for both to show running

### Step 3: Copy Project

1. Open Terminal
2. Run: `cp -r "~/Desktop/Secure-transport/Mohit" /Applications/XAMPP/xamppfiles/htdocs/`

### Step 4: Create Database

1. Terminal: `/Applications/XAMPP/xamppfiles/bin/mysql -u root < ~/Desktop/Secure-transport/Mohit/database/schema.sql`
2. Or use phpMyAdmin at: http://localhost/phpmyadmin

### Step 5: Access Application

1. Browser: **http://localhost/Mohit/public/**
2. Login with: admin@polling.test / admin123

## Setup (Linux - Ubuntu/Debian)

### Step 1: Install Required Packages

```bash
sudo apt-get update
sudo apt-get install php8.0 php8.0-mysql mysql-server apache2 libapache2-mod-php8.0
```

### Step 2: Enable Apache Modules

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Step 3: Start MySQL

```bash
sudo systemctl start mysql
sudo systemctl enable mysql
```

### Step 4: Copy Project

```bash
sudo cp -r ~/Desktop/Secure-transport/Mohit /var/www/html/
sudo chown -R www-data:www-data /var/www/html/Mohit
```

### Step 5: Create Database

```bash
mysql -u root -p < ~/Desktop/Secure-transport/Mohit/database/schema.sql
```

### Step 6: Access Application

```
Browser: http://localhost/Mohit/public/
Login: admin@polling.test / admin123
```

## Database Configuration

Edit `bootstrap/app.php` if you need custom credentials:

```php
define('DB_HOST', 'localhost');      // Database host
define('DB_NAME', 'polling_system');  // Database name
define('DB_USER', 'root');           // Database user
define('DB_PASS', '');               // Database password (empty for XAMPP)
define('DB_CHARSET', 'utf8mb4');     // Character set
```

Default configuration assumes:
- Host: localhost (127.0.0.1)
- Database: polling_system
- User: root
- Password: (empty for XAMPP, set for production)

## Test the Installation

### 1. Admin Login
1. Navigate to: http://localhost/Mohit/public/
2. Click "Use test credentials" or enter:
   - Email: admin@polling.test
   - Password: admin123
3. Click Login

### 2. Create a Test Poll
1. Click "Create Poll" (Admin only)
2. Enter poll question: "Test Poll?"
3. Add options: "Option A", "Option B", "Option C"
4. Click "Create Poll"

### 3. Vote and Test Real-Time Updates
1. Vote on the poll by clicking an option
2. Watch the results update in real-time (every 1 second)
3. Vote again on another poll to see IP restriction working
4. Notice you can vote on the first poll from different devices/IPs

### 4. Test Admin Features
1. Go to Admin Dashboard (Admin only)
2. Click on a poll to see voters
3. Click "Release Vote" to let a voter re-vote
4. View complete vote history

## Troubleshooting

### Apache Not Starting
- Check if port 80 is in use by another application
- Try changing port in xampp/apache/conf/httpd.conf
- Restart XAMPP Control Panel

### MySQL Connection Error
- Verify MySQL is running (check XAMPP panel)
- Verify database credentials in bootstrap/app.php
- Ensure polling_system database exists

### Page Not Found (404)
- Verify mod_rewrite is enabled: `a2enmod rewrite`
- Check that .htaccess file exists in public/ folder
- Verify Apache is pointing to correct directory
- Make sure you're accessing /public/ folder

### Blank Page
- Check PHP error logs: xampp/apache/logs/error.log
- Enable error display in bootstrap/app.php
- Verify all dependencies are installed
- Check database connection in bootstrap/app.php

### IP Restriction Not Working
- Clear browser cache
- Test from different devices or IP addresses
- Check that vote stored correctly in database
- Verify UNIQUE constraint on votes table

## Production Setup (HTTPS)

For production deployment:

1. Obtain SSL certificate (Let's Encrypt is free)
2. Configure HTTPS in your web server
3. Update bootstrap/app.php with production credentials
4. Enable rate limiting
5. Set secure session cookies
6. Use strong database passwords
7. Remove debug mode
8. Set proper file permissions

## File Permissions

Recommended permissions for security:

```bash
# Linux/Mac
chmod 755 bootstrap/app.php
chmod 755 public/index.php
chmod 555 app/Core/VotingEngine.php
chmod 644 database/schema.sql
chmod 644 .htaccess
```

## Next Steps

1. Read QUICK-START.md for feature overview
2. Read README.md for architecture details
3. Read API.md for endpoint reference
4. Check database/schema.sql for table structure

## Support

For issues or questions:

1. Check the QUICK-START.md guide
2. Review README.md troubleshooting section
3. Check SETUP.md for detailed installation steps
4. Review LOCALHOST-SETUP.md for local development

---

Professional Real-Time Live Poll Platform | Setup Guide
