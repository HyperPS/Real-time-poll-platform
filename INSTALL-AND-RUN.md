# INSTALLATION AND RUNNING GUIDE - Professional Real-Time Poll Platform

## OVERVIEW

This guide will help you set up and run the Real-Time Live Poll Platform on your local machine. The application is production-ready and includes complete authentication, IP-restricted voting, real-time results, and admin features.

**Project Status:** Professional | All emojis removed | Production-ready code

---

## QUICK CHECKLIST

- [ ] PHP 8.0+ installed (via XAMPP)
- [ ] MySQL running (via XAMPP) 
- [ ] Database schema imported (database/schema.sql)
- [ ] Apache/PHP server started
- [ ] Application accessible at http://localhost/Mohit/public/
- [ ] Can login with test credentials
- [ ] Can create polls and vote

---

## SYSTEM REQUIREMENTS

| Requirement | Minimum | Recommended |
|---|---|---|
| PHP | 8.0 | 8.1+ |
| MySQL | 5.7 | 8.0+ |
| Apache | 2.4 | 2.4.41+ |
| RAM | 512MB | 2GB+ |
| Storage | 100MB | 500MB |
| Browser | Modern (Chrome, Firefox, Safari, Edge) | Latest version |

---

## INSTALLATION METHODS

### METHOD 1: XAMPP (EASIEST - Windows/Mac/Linux)

#### Step 1: Install XAMPP

**Windows:**
1. Download from: https://www.apachefriends.org/
2. Choose PHP 8.0+ version
3. Run installer and complete setup
4. Default location: C:\xampp\

**Mac:**
1. Download XAMPP for Mac
2. Open .dmg file and drag XAMPP to Applications
3. Launch XAMPP control panel

**Linux:**
1. Download XAMPP for Linux
2. `chmod +x xampp-linux-*.run`
3. `sudo ./xampp-linux-*.run`

#### Step 2: Copy Project Files

**Windows:**
```
Copy: C:\Users\sarve\Desktop\Secure-transport\Mohit
To:   C:\xampp\htdocs\Mohit
```

**Mac:**
```bash
cp -r ~/Desktop/Secure-transport/Mohit /Applications/XAMPP/xamppfiles/htdocs/
```

**Linux:**
```bash
sudo cp -r ~/Desktop/Secure-transport/Mohit /opt/lampp/htdocs/
```

#### Step 3: Start Services

**Windows:**
1. Open XAMPP Control Panel (xampp-control.exe from C:\xampp)
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Wait for both to show "Running"

**Mac:**
1. Open XAMPP Application Manager
2. Click "Start" next to Apache
3. Click "Start" next to MySQL

**Linux:**
```bash
sudo /opt/lampp/manager-linux-x64.run

# Or use command line:
sudo /opt/lampp/lampp start
```

#### Step 4: Create Database

**Option A: Using phpMyAdmin (GUI - Recommended)**

1. Open browser: http://localhost/phpmyadmin
2. Click "SQL" tab or "New" database
3. Copy all text from: `database/schema.sql`
4. Paste into phpMyAdmin SQL query box
5. Click "Go" or "Execute"
6. Verify tables created in left sidebar under "polling_system"

**Option B: Using Command Line**

Windows Command Prompt:
```cmd
cd "C:\xampp\mysql\bin"
mysql -u root < "path\to\database\schema.sql"
```

Mac Terminal:
```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root < ~/Desktop/Secure-transport/Mohit/database/schema.sql
```

Linux:
```bash
mysql -u root < ~/Desktop/Secure-transport/Mohit/database/schema.sql
```

#### Step 5: Verify Installation

1. Open browser to: http://localhost/Mohit/public/check-status.php
2. System should show all GREEN checkmarks
3. If database shows as connected, you're ready!

#### Step 6: Access Application

Open: **http://localhost/Mohit/public/**

Login with:
- Email: admin@polling.test
- Password: admin123

---

### METHOD 2: Standalone PHP Server (Development Only)

For quick local testing without Apache (PHP 5.4+):

```bash
cd "path\to\Mohit\public"
php -S localhost:8000
```

Access: http://localhost:8000/

Note: Requires database to be set up separately

---

### METHOD 3: Docker (Linux/Mac/Windows Professional)

#### Create docker-compose.yml:

```yaml
version: '3'
services:
  web:
    image: php:8.0-apache
    ports:
      - "80:80"
    volumes:
      - ./:/var/www/html
    environment:
      - PHP_DISPLAY_ERRORS=On
  
  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=root
      - MYSQL_DATABASE=polling_system
    ports:
      - "3306:3306"
    volumes:
      - ./database:/docker-entrypoint-initdb.d
```

#### Run:
```bash
docker-compose up -d
# Wait 30 seconds for database initialization
docker exec container_name mysql -u root -proot polling_system < database/schema.sql
```

Access: http://localhost/Mohit/public/

---

## CONFIGURATION

### Database Configuration (Optional - Only if Using Non-Default)

Edit: `bootstrap/app.php`

```php
// Line 15-20: Edit these if credentials differ from XAMPP defaults
define('DB_HOST', 'localhost');      // 127.0.0.1 if localhost fails
define('DB_NAME', 'polling_system');  // Must match created database
define('DB_USER', 'root');           // Database username
define('DB_PASS', '');               // Empty for XAMPP default
define('DB_CHARSET', 'utf8mb4');     // Keep as is
```

### Apache Configuration (If Not Using XAMPP)

Ensure .htaccess rewriting is enabled:

```bash
# Linux - Enable mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# macOS (via Homebrew)
brew install httpd
sudo brew services restart httpd
```

Virtual Host Config (optional):
```apache
<VirtualHost *:80>
    ServerName polling.local
    DocumentRoot "C:/xampp/htdocs/Mohit/public"
    
    <Directory "C:/xampp/htdocs/Mohit/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## VERIFY INSTALLATION

### Step 1: Check System Status

Open: http://localhost/Mohit/public/check-status.php

This page shows:
- PHP version and extensions
- Project files validation
- Database connection status
- Sample data count
- Helpful troubleshooting tips

### Step 2: Test Login

1. Navigate to: http://localhost/Mohit/public/
2. Try logging in with:
   - Admin: admin@polling.test / admin123
   - User: user@polling.test / user123
3. Should see dashboard with active polls

### Step 3: Test Core Features

**Admin:**
1. Click "Create Poll"
2. Enter: "Is this working?" 
3. Add options: "Yes", "No", "Maybe"
4. Click "Create Poll"

**Any User:**
1. Vote on the poll
2. Watch results update in real-time (every 1 second)
3. Try voting on another poll
4. Notice you can vote on first poll from different device

**Admin:**
1. Go to Admin Dashboard
2. Click on a poll name
3. See all voters with their IPs
4. Click "Release Vote" to let voter vote again
5. View complete vote history

---

## LOCALHOST URLS

| Feature | URL |
|---|---|
| **Main App** | http://localhost/Mohit/public/ |
| **Status Check** | http://localhost/Mohit/public/check-status.php |
| **phpMyAdmin** | http://localhost/phpmyadmin |
| **XAMPP Dashboard** | http://localhost |

---

## TROUBLESHOOTING

### Issue: "404 Not Found"

**Solution:**
1. Verify you're accessing `/public/` folder
2. Check that `.htaccess` file exists in `public/` folder
3. Verify Apache `mod_rewrite` is enabled
4. For Apache, ensure DirectoryIndex includes index.php

### Issue: "Blank White Page"

**Solution:**
1. Open browser console (F12 → Console tab)
2. Note any JavaScript errors
3. Check XAMPP Apache error log: `C:\xampp\apache\logs\error.log`
4. Verify `bootstrap/app.php` is readable
5. Check PHP syntax: `php -l bootstrap/app.php`

### Issue: "Can't Connect to Database"

**Solution:**
1. Verify MySQL is running (check XAMPP panel)
2. Verify database exists: `http://localhost/phpmyadmin`
3. Check credentials in `bootstrap/app.php` match your setup
4. Try: `mysql -u root` in command line
5. Re-import `database/schema.sql` if tables missing

### Issue: "Login Fails with Test Credentials"

**Solution:**
1. Check that users table was created
2. Verify password hashes exist in database
3. Clear browser cookies and try again
4. Try MySQL query: `SELECT * FROM users;`
5. Re-import database schema if users table corrupt

### Issue: "IP Restriction Not Working"

**Solution:**
1. Test from different devices/browsers
2. Clear browser cache completely
3. Use VPN or mobile hotspot for different IP
4. Check vote_history table entries
5. Verify UNIQUE constraint on votes table

### Issue: "Can't Access from Other Computers"

**Solution:**

For Windows XAMPP:
1. Edit: `C:\xampp\apache\conf\httpd.conf`
2. Find: `Listen 80`
3. Change to: `Listen 0.0.0.0:80`
4. Restart Apache
5. Access from: `http://YOUR-IP-ADDRESS/Mohit/public/`

### Issue: "Permission Denied Errors"

**Solution:**

Windows:
1. Right-click folder → Properties
2. Click Security tab
3. Edit → Select user → Full Control
4. Apply → OK

Linux:
```bash
sudo chown -R www-data:www-data /var/www/html/Mohit
sudo chmod -R 755 /var/www/html/Mohit
```

---

## DATABASE INFORMATION

### Tables Created

1. **users** - Application users (admin & regular)
   - Columns: id, name, email, password, role, timestamps
   - Test data: 2 users (admin + regular)

2. **polls** - Poll questions
   - Columns: id, question, status, created_by, timestamps
   - Empty by default (create via UI or manually)

3. **poll_options** - Poll answer options
   - Columns: id, poll_id, option_text, created_at
   - Linked to polls via poll_id

4. **votes** - Active votes only
   - Columns: id, poll_id, option_id, ip_address, voted_at, is_active
   - UNIQUE constraint: (poll_id, ip_address, is_active) - prevents duplicate votes

5. **vote_history** - Complete audit trail
   - Columns: id, poll_id, option_id, ip_address, action_type, timestamp, details
   - Records all actions: vote, release, revote

6. **sessions** - Laravel-style session storage
   - Used for session management

### Test Data

**Users:**
```
Email: admin@polling.test
Password: admin123 (bcrypt hash)
Role: admin

Email: user@polling.test  
Password: user123 (bcrypt hash)
Role: user
```

### Database Credentials (Default XAMPP)

```
Host: localhost
Port: 3306
Database: polling_system
Username: root
Password: (empty)
Charset: utf8mb4
```

---

## FILE STRUCTURE

```
Mohit/
├── app/
│   ├── Core/
│   │   └── VotingEngine.php         (Vote logic, IP validation)
│   ├── Http/Controllers/
│   │   ├── AuthController.php        (Login/logout)
│   │   ├── PollController.php        (Poll CRUD)
│   │   ├── VoteController.php        (Vote casting)
│   │   └── AdminController.php       (Admin functions)
│   ├── Models/
│   │   ├── User.php
│   │   ├── Poll.php
│   │   └── PollOption.php
│   └── helpers.php                   (40+ utilities)
│
├── bootstrap/
│   └── app.php                       (Configuration & initialization)
│
├── public/
│   ├── index.php                     (Application entry point)
│   ├── check-status.php              (System status checker)
│   ├── .htaccess                     (Apache rewrite rules)
│   ├── css/
│   │   └── style.css                 (3D effects, animations)
│   └── js/
│       ├── app.js                    (AJAX utilities)
│       └── poll-voting.js            (Real-time voting)
│
├── resources/views/
│   ├── auth/
│   │   └── login.blade.php          (Login form)
│   ├── polls/
│   │   ├── dashboard.blade.php      (Poll listing)
│   │   ├── create.blade.php         (Create poll)
│   │   └── show.blade.php           (Vote interface)
│   ├── admin/
│   │   └── dashboard.blade.php      (Admin panel)
│   └── layouts/
│       └── app.blade.php            (Main layout)
│
├── routes/
│   └── web.php                       (Route configuration)
│
├── database/
│   └── schema.sql                    (Database creation script)
│
├── config/
│   └── database.php                  (Database config template)
│
├── Documentation/
│   ├── README.md                     (Overview & features)
│   ├── LOCALHOST-SETUP.md            (Local development setup)
│   ├── SETUP.md                      (Installation guide)
│   ├── QUICK-START.md                (5-minute setup)
│   ├── API.md                        (Endpoint reference)
│   └── More...
│
└── Tools/
    ├── setup-windows.bat             (Windows setup helper)
    └── .env.example                  (Environment template)
```

---

## TESTING CHECKLIST

After installation, verify all features work:

### Authentication
- [ ] Can login with admin credentials
- [ ] Can login with user credentials
- [ ] Login error handling works
- [ ] Logout button works
- [ ] Session persists across pages
- [ ] Unauthorized access redirects to login

### Polls
- [ ] Admin can create polls
- [ ] Polls appear on dashboard
- [ ] Can view poll details
- [ ] Poll shows all options
- [ ] Can see vote counts
- [ ] Results update without page reload

### Voting
- [ ] Can vote on first attempt
- [ ] Results update in real-time
- [ ] Can't vote twice from same IP
- [ ] Vote history records all votes
- [ ] Audit trail shows vote action
- [ ] IP address stored correctly

### Admin Features
- [ ] Admin dashboard shows all polls
- [ ] Can see list of voters
- [ ] Can release votes for re-voting
- [ ] Vote history shows release action
- [ ] Can revote after release
- [ ] Vote history shows revote action

### Performance
- [ ] Page loads within 2 seconds
- [ ] Results update smoothly
- [ ] No JavaScript errors in console
- [ ] Database queries optimize
- [ ] 3D effects render smoothly
- [ ] No memory leaks on long usage

---

## PRODUCTION DEPLOYMENT

Before going live:

1. Change database password in `bootstrap/app.php`
2. Enable HTTPS (SSL certificate required)
3. Set `error_reporting(E_ERROR)` to hide errors
4. Update session configuration for security
5. Set up automated backups
6. Configure rate limiting
7. Enable only necessary PHP extensions
8. Set appropriate file permissions
9. Set up error logging to files
10. Test with real-world data and load

---

## SUPPORT & RESOURCES

### Built With
- PHP 8.0+ - Backend language
- Laravel-style Routing - URL handling
- Core PHP - Voting logic (VotingEngine)
- MySQL 5.7+ - Database
- Bootstrap 5 - UI framework
- jQuery 3.6 - AJAX functionality
- CSS3 - 3D effects and animations

### Documentation Files
- README.md - Feature overview
- QUICK-START.md - 5-minute setup
- SETUP.md - Detailed installation
- LOCALHOST-SETUP.md - Local development
- API.md - All endpoints documented
- FILE-STRUCTURE.md - Project organization
- PROJECT-COMPLETE.md - Feature summary
- DELIVERY.md - Requirements checklist

### Common Commands

```bash
# Check PHP version
php -v

# Test PHP syntax
php -l bootstrap/app.php

# Start built-in PHP server (port 8000)
php -S localhost:8000

# Import database
mysql -u root < database/schema.sql

# Verify database
mysql -u root -e "USE polling_system; SHOW TABLES;"
```

---

## NEXT STEPS

1. Follow installation method above
2. Run http://localhost/Mohit/public/check-status.php
3. Fix any red errors shown
4. Login and test features
5. Review QUICK-START.md for feature tour
6. Check API.md for endpoint reference
7. Deploy when ready

---

**Real-Time Live Poll Platform | Professional & Production Ready**
