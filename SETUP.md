#  Setup Instructions - Real-Time Poll Platform

Complete step-by-step guide to set up and run the polling system locally.

---

## Prerequisites

### System Requirements
- **Windows 10/11**, **macOS**, or **Linux**
- **PHP 8.0+** (with PDO extension)
- **MySQL 5.7+** or **MariaDB 10.3+**
- **Apache 2.4+** (with mod_rewrite) or **Nginx**
- **Git** (optional)

### Required PHP Extensions
- `pdo_mysql`
- `json`
- `filter`
- `session`

---

## Option 1: Local Development (Windows)

### Step 1: Install Prerequisites

#### 1a. Install XAMPP or WAMP
- Download XAMPP from: https://www.apachefriends.org/ (recommended)
- Or WAMP from: http://www.wampserver.com/
- Run installer and select PHP 8.0+

#### 1b. Verify Installation
```bash
# Open Command Prompt
php -v
mysql -V
```

### Step 2: Create Database

#### Method 1: Using phpMyAdmin
1. Open browser: `http://localhost/phpmyadmin`
2. Click "New" database
3. Name: `polling_system`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Go to "Import" tab
7. Select `database/schema.sql`
8. Click "Go"

#### Method 2: Using Command Line
```bash
# Open Command Prompt
mysql -u root -p
# Enter password (usually empty for XAMPP)

# In MySQL shell:
CREATE DATABASE polling_system;
USE polling_system;
SOURCE C:/path/to/polling_system/database/schema.sql;
EXIT;
```

### Step 3: Configure Application

1. Open `bootstrap/app.php`
2. Update database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'polling_system');
define('DB_USER', 'root');
define('DB_PASS', ''); // Usually empty for XAMPP
```

### Step 4: Create Virtual Host (Apache)

#### For XAMPP:

1. Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Add at end of file:

```apache
<VirtualHost *:80>
    ServerName polling.local
    DocumentRoot "C:\xampp\htdocs\Secure-transport\Mohit\public"
    
    <Directory "C:\xampp\htdocs\Secure-transport\Mohit\public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Open `C:\Windows\System32\drivers\etc\hosts` (as Administrator)
4. Add line at end:
```
127.0.0.1 polling.local
```

5. Restart Apache from XAMPP Control Panel

### Step 5: Start Services

1. Open XAMPP Control Panel
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Wait for both to show green

### Step 6: Access Application

1. Open browser
2. Navigate to: `http://polling.local/`
3. Or: `http://localhost/Secure-transport/Mohit/public/`

---

## Option 2: Development with IIS (Windows)

### Step 1: Enable IIS
1. Control Panel  Programs  Programs and Features
2. Click "Turn Windows features on or off"
3. Check: Internet Information Services
4. Expand and check: CGI
5. Click OK

### Step 2: Install PHP

1. Download PHP 8.0+ from: https://windows.php.net/download/
2. Extract to: `C:\php`
3. Copy `php.ini-development` to `php.ini`
4. Enable extensions:

In php.ini, uncomment:
```ini
extension=pdo_mysql
extension=json
extension=filter
```

### Step 3: Configure IIS

1. Open Internet Information Services (IIS) Manager
2. Right-click Application Pools  Add Application Pool
   - Name: polling
   - .NET CLR version: No Managed Code
   - Managed pipeline mode: Integrated
3. Right-click Sites  Add Website
   - Site name: polling
   - Physical path: `C:\path\to\Secure-transport\Mohit\public`
   - Binding: polling.local
4. Add to hosts file (as Administrator):

```
127.0.0.1 polling.local
```

### Step 4: Create Database

Use Step 2 from XAMPP guide above.

### Step 5: Access Application

Open browser: `http://polling.local/`

---

## Option 3: Linux/Mac Development

### Step 1: Install Requirements

#### Ubuntu/Debian:
```bash
sudo apt update
sudo apt install apache2 php8.0 php8.0-mysql php8.0-pdo mysql-server
sudo a2enmod rewrite
sudo systemctl restart apache2
sudo mysql_secure_installation
```

#### macOS (with Homebrew):
```bash
brew install php apache2 mysql
# Follow Homebrew post-installation instructions
```

### Step 2: Create Database

```bash
# Create database
mysql -u root -p < database/schema.sql
```

### Step 3: Configure Application

Edit `bootstrap/app.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'polling_system');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

### Step 4: Create Virtual Host

#### Ubuntu/Debian:

Create `/etc/apache2/sites-available/polling.conf`:

```apache
<VirtualHost *:80>
    ServerName polling.local
    ServerAdmin admin@polling.local
    DocumentRoot /path/to/Secure-transport/Mohit/public

    <Directory /path/to/Secure-transport/Mohit/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/polling_error.log
    CustomLog ${APACHE_LOG_DIR}/polling_access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite polling
sudo systemctl reload apache2
```

Add to `/etc/hosts`:
```
127.0.0.1 polling.local
```

### Step 5: Start Services

```bash
# Ubuntu/Debian
sudo systemctl start apache2
sudo systemctl start mysql

# macOS
brew services start apache2
brew services start mysql
```

### Step 6: Access Application

Open browser: `http://polling.local/`

---

## Troubleshooting

### Issue: Database Connection Failed

**Solution**:
1. Check MySQL is running
   ```bash
   mysql -u root -p -e "SELECT 1;"
   ```
2. Verify credentials in `bootstrap/app.php`
3. Try resetting MySQL:
   ```bash
   mysql -u root -p < database/schema.sql
   ```

### Issue: Mod_rewrite Not Working

**Solution (Apache)**:
1. Enable mod_rewrite:
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```
2. Verify `.htaccess` is in `public/` folder
3. Check Apache error log

### Issue: Permission Denied

**Solution**:
1. Set correct permissions:
   ```bash
   chmod 755 -R /path/to/Secure-transport/Mohit
   chmod 777 /path/to/Secure-transport/Mohit/public
   ```

### Issue: 404 Errors on Routes

**Solution**:
1. Check `.htaccess` is present in `public/` folder
2. Verify Apache mod_rewrite is enabled
3. Reload Apache configuration

### Issue: Login Not Working

**Solution**:
1. Verify database table has admin user:
   ```sql
   SELECT * FROM users WHERE email = 'admin@polling.test';
   ```
2. Check PHP session settings in `php.ini`
3. Clear browser cookies and try again

### Issue: AJAX Requests Failing

**Solution**:
1. Open browser DevTools (F12)
2. Check Network tab for error responses
3. Check Console for JavaScript errors
4. Verify API endpoints in network requests

---

## Verification Checklist

After setup, verify:

- [ ] MySQL is running and accessible
- [ ] Database `polling_system` exists
- [ ] Users table has at least 2 users
- [ ] Apache/web server is running
- [ ] Mod_rewrite is enabled
- [ ] Can login with admin@polling.test / admin123
- [ ] Can access dashboard after login
- [ ] Can create a poll (if admin)
- [ ] Can vote on a poll (all users)
- [ ] Real-time results update
- [ ] Admin panel accessible (admin only)
- [ ] Vote release works (admin only)

---

## Quick Commands Reference

### MySQL
```bash
# Login
mysql -u root -p

# Create database
CREATE DATABASE polling_system;

# Import schema
SOURCE database/schema.sql;

# View users
SELECT * FROM users;

# View polls
SELECT * FROM polls;

# View votes
SELECT * FROM votes;

# Check vote history
SELECT * FROM vote_history;
```

### Apache (Linux)
```bash
# Start Apache
sudo systemctl start apache2

# Stop Apache
sudo systemctl stop apache2

# Restart Apache
sudo systemctl restart apache2

# Enable mod_rewrite
sudo a2enmod rewrite

# Check error log
sudo tail -f /var/log/apache2/error.log
```

### PHP Built-in Server (Development Only)
```bash
# Run PHP development server
cd public
php -S localhost:8000

# Then open: http://localhost:8000
```

---

## Security Notes for Production

Before deploying to production:

1. **Database**
   - Use strong MySQL password
   - Restrict MySQL connections to localhost only
   - Regular backups

2. **PHP**
   - Set `display_errors = Off` in php.ini
   - Enable error logging
   - Use strong session settings

3. **Apache**
   - Enable HTTPS/SSL
   - Disable directory listing
   - Restrict access to sensitive files
   - Use security headers

4. **Application**
   - Change CSRF token secret
   - Implement rate limiting
   - Use environment variables for secrets
   - Regular security audits

---

## Support & Help

If you encounter issues:

1. Check error logs:
   - Apache: `error.log`
   - MySQL: `error.log`
   - PHP: `php_errors.log`

2. Review README.md for architecture details

3. Check database schema in `database/schema.sql`

4. Review VotingEngine logic in `app/Core/VotingEngine.php`

---

**Setup Complete!  Enjoy the Real-Time Poll Platform!**
