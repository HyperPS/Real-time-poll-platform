#  Quick Start - 5 Minutes to Live

Get the poll system running in just 5 minutes!

---

##  Super Quick Setup (Windows with XAMPP)

### Step 1: Create Database (1 minute)
1. Open `database/schema.sql` in any text editor
2. Copy all the SQL code
3. Open phpMyAdmin: `http://localhost/phpmyadmin`
4. Paste and run the SQL

### Step 2: Update Configuration (1 minute)
Edit `bootstrap/app.php` - find this section:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'polling_system');
define('DB_USER', 'root');
define('DB_PASS', '');  // Leave empty if XAMPP
```

### Step 3: Enable Mod_Rewrite (1 minute)
1. Open XAMPP Control Panel
2. Click "Config" next to Apache
3. Edit `httpd.conf`
4. Find `LoadModule rewrite_module modules/mod_rewrite.so` and uncomment it
5. Save and restart Apache

### Step 4: Access Application (1 minute)
Open browser: `http://localhost/Secure-transport/Mohit/public/`

### Step 5: Login (1 minute)
```
Email: admin@polling.test
Password: admin123
```

---

##  Done! You're Ready to Go

Now you can:
-  Create polls (Admin only) 
-  Vote on polls
-  See real-time results
-  Release votes (Admin only)
-  View vote history

---

##  Key URLs

After setup, access these pages:

| Page | URL | Access |
|------|-----|--------|
| Login | `http://localhost/.../public/` | Anyone |
| Dashboard | `http://localhost/.../public/dashboard` | Logged in |
| Admin | `http://localhost/.../public/admin/dashboard` | Admin only |
| Create Poll | `http://localhost/.../public/polls/create` | Admin only |

---

##  Troubleshooting

### "Database Connection Failed"
 Make sure MySQL is running in XAMPP  
 Check credentials in `bootstrap/app.php`

### "404 Page Not Found" on results/voting
 Apache mod_rewrite not enabled  
 Restart Apache

### Login fails
 Check database has users  
 Try: `SELECT * FROM users;` in phpMyAdmin

---

##  Need More Help?

- **Setup Guide**: Read `SETUP.md` for detailed instructions
- **API Reference**: Read `API.md` for all endpoints
- **Full Documentation**: Read `README.md` for complete overview

---

##  Try These Features

### Create a Poll (As Admin)
1. Go to Admin Panel  Create Poll
2. Enter question: "Best Programming Language?"
3. Add options: PHP, Python, JavaScript
4. Click Create

### Vote on Poll (Any User)
1. Logout (if admin)
2. Login as: user@polling.test / user123
3. Click "Vote Now" on any poll
4. Select option and submit
5. See real-time results!

### Release Vote (As Admin)
1. Go to Admin Panel
2. Select the poll you created
3. Click "Manage" 
4. Click "Manage Voters" tab
5. Click "Release" on any IP
6. That IP can now vote again!

---

##  Security Notes

- All data validated and sanitized
- All database queries use prepared statements
- CSRF protection enabled
- Passwords hashed with bcrypt
- IP addresses validated

---

##  Pro Tips

1. **Multiple Users**: Create more test accounts by adding to `users` table
2. **Real-Time Feeling**: Open in 2 browser windows and vote from each
3. **Kill Vote**: Admin can always release a vote to test
4. **Check Database**: Use phpMyAdmin to see `vote_history` table
5. **Mobile Test**: Open on your phone to see responsive design

---

##  Time Check

If you followed these steps:
- Database setup: ~1 min
- Configuration: ~1 min  
- Enable mod_rewrite: ~1 min
- Access app: ~1 min
- Login: ~1 min

**Total: ~5 minutes! **

---

##  Congratulations!

You now have a fully functional, production-ready real-time polling system!

**Start polling! **

---

## Next Steps (Optional)

1. Read full `README.md` for complete features
2. Check `API.md` for all endpoints
3. Test with multiple browser windows
4. Try admin features
5. Review code in `app/Core/VotingEngine.php`

---

**Happy Polling! **
