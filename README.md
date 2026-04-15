# Real-Time Live Poll Platform with IP Restriction and Admin Moderation

A comprehensive real-time polling system built with **Laravel**, **Core PHP**, **MySQL**, and **AJAX**. This platform supports IP-restricted voting, admin moderation, vote history tracking, and real-time updates with 3D UI effects.

## Features

### Core Features
- **User Authentication** - Secure login system
- **IP-Restricted Voting** - Only one vote per IP per poll
- **Real-Time Results** - AJAX polling updates every 1 second
- **Admin Panel** - Manage polls and vote releases
- **Vote History** - Full audit trail with action tracking
- **No Page Reloads** - Pure AJAX interactions
- **3D Effects** - Smooth card transitions and hover effects
- **Responsive Design** - Works on all devices

### Technical Features
- Clean MVC Architecture
- Separation of Concerns (Laravel + Core PHP)
- Prepared Statements (SQL Injection Protection)
- CSRF Protection
- Input Validation & Sanitization
- Professional UI with Bootstrap 5

---

## Quick Start

### Prerequisites
- PHP 8.0+
- MySQL 5.7+
- Web server (Apache/Nginx with PHP support)

### Installation Steps

#### 1. **Create Database**
Run the SQL schema to create the database and tables:

```bash
# Using MySQL command line
mysql -u root -p < database/schema.sql

# Or use phpMyAdmin to import database/schema.sql
```

#### 2. **Configure Database**
Edit `bootstrap/app.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'polling_system');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

#### 3. **Set Up Web Server**
Point your web server to the `public/` directory:

**Apache (.htaccess)**:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ index.php [L]
</IfModule>
```

**Nginx**:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

#### 4. **Access Application**
Open your browser and navigate to:
```
http://localhost/Secure-transport/Mohit/public/
```

---

## Test Accounts

### Admin Account
- **Email**: admin@polling.test
- **Password**: admin123
- **Role**: Admin (can create polls and manage votes)

### Regular User Account
- **Email**: user@polling.test
- **Password**: user123
- **Role**: User (can only vote)

---

## Project Structure

```
Mohit/
 app/
    Core/
       VotingEngine.php          # Core voting logic (IP restrictions, validations)
    Http/
       Controllers/
           AuthController.php    # Authentication
           PollController.php    # Poll management
           VoteController.php    # Voting AJAX endpoints
           AdminController.php   # Admin panel
    Models/
        User.php                  # User model
        Poll.php                  # Poll model
        PollOption.php            # Poll option model
 bootstrap/
    app.php                       # Application bootstrap & initialization
 public/
    index.php                     # Entry point
    css/
       style.css                 # Custom styles with 3D effects
    js/
        app.js                    # Core AJAX functions
        poll-voting.js            # Voting interface manager
 resources/
    views/
        auth/
           login.blade.php       # Login page
        polls/
           dashboard.blade.php   # User dashboard
           show.blade.php        # Individual poll view
           create.blade.php      # Create poll (admin)
        admin/
           dashboard.blade.php   # Admin dashboard
           manage-poll.blade.php # Manage individual poll
        layouts/
            app.blade.php         # Main layout
 routes/
    web.php                       # Route configuration
 database/
    schema.sql                    # Database schema
    migrations/                   # Migration files
 config/
     database.php                  # Database configuration
```

---

## Architecture Overview

### Layer 1: Laravel (Routing & Authentication)
- Handles HTTP requests through Router
- Manages user authentication sessions
- Controls access to protected routes

### Layer 2: Controllers (MVC Logic)
- **AuthController**: Login, logout, authentication
- **PollController**: Poll CRUD operations
- **VoteController**: AJAX voting endpoints
- **AdminController**: Admin functions

### Layer 3: Core PHP (Business Logic)
- **VotingEngine**: Contains the critical voting logic
  - IP validation and restriction
  - Vote casting and release
  - Vote history tracking
  - Results calculation

### Layer 4: Database (MySQL)
- Users, Polls, Options, Votes, Vote History tables
- Indexed for performance
- Prepared statements for security

### Layer 5: Frontend (AJAX & UI)
- jQuery-based AJAX calls
- Real-time result updates
- 3D hover effects with CSS
- Bootstrap responsive design

```

      Frontend (AJAX + UI)           
   jQuery, Bootstrap, 3D Effects     

               

      Laravel Router & Auth          
   Route Dispatcher, Session Mgmt    

               

      Controllers (MVC)              
  Auth, Poll, Vote, Admin            

               

     Core PHP - VotingEngine         
  Vote Logic, IP Validation, Audit   

               

      Database (MySQL)               
  Normalized Tables, Indexes         

```

---

## Security Features

### Authentication
- Password hashing with bcrypt
- Session-based authentication
- Route protection for authenticated users

### SQL Injection Prevention
- PDO prepared statements everywhere
- Parameter binding
- Type casting

### CSRF Protection
- Token generation on session start
- Token validation for forms

### Input Validation
- Email validation
- IP address validation (IPv4 & IPv6)
- Data sanitization
- HTML escaping in views

### Vote Security
- IP address verification
- Duplicate vote prevention
- Vote audit logging
- Admin action tracking

---

## API Endpoints

### Authentication
- `POST /login` - User login
- `GET /logout` - User logout

### Polls
- `GET /api/polls` - Get all active polls
- `GET /api/polls/{pollId}` - Get specific poll

### Voting
- `POST /api/vote/cast` - Cast a vote
- `GET /api/vote/status` - Check if IP has voted
- `GET /api/results` - Get poll results

### Admin
- `GET /api/admin/voters` - Get voters by poll
- `POST /api/admin/vote/release` - Release a vote
- `GET /api/admin/vote-history` - Get vote history
- `POST /api/admin/poll/status` - Toggle poll status
- `POST /api/admin/poll/delete` - Delete poll

---

##  Database Schema

### Users Table
```sql
- id (PK)
- name
- email (UNIQUE)
- password (hashed)
- role (admin/user)
- created_at, updated_at
```

### Polls Table
```sql
- id (PK)
- question
- status (active/inactive)
- created_by (FK to users)
- created_at, updated_at
```

### Poll Options Table
```sql
- id (PK)
- poll_id (FK)
- option_text
- created_at
```

### Votes Table (Active Votes)
```sql
- id (PK)
- poll_id (FK)
- option_id (FK)
- ip_address
- voted_at
- is_active (boolean)
- unique constraint: (poll_id, ip_address, is_active)
```

### Vote History Table (Audit Trail)
```sql
- id (PK)
- poll_id (FK)
- option_id (FK)
- ip_address
- action_type (vote/release/revote)
- timestamp
- details (JSON)
```

---

## Frontend Features

### Real-Time Updates
- Poll results refresh every 1 second
- AJAX polling (no WebSockets required)
- Smooth animations

### 3D Effects
- Card hover transforms
- Depth shadows
- Parallax mouse tracking
- Smooth transitions

### Responsive Design
- Mobile-first approach
- Bootstrap grid system
- Optimized for all screen sizes

---

##  Core PHP - VotingEngine Class

Located in: `app/Core/VotingEngine.php`

### Key Methods

```php
// Cast a vote
castVote($pollId, $optionId, $ipAddress)

// Release a vote by IP
releaseVote($pollId, $ipAddress)

// Handle revote after release
reVote($pollId, $optionId, $ipAddress)

// Get poll results
getPollResults($pollId)

// Get vote history
getVoteHistory($pollId, $ipAddress = null)

// Get voters for a poll
getVotersByPoll($pollId)
```

### IP Restriction Logic
```php
// Checks if IP already has an active vote on this poll
$existingVote = $this->getActiveVoteByIp($pollId, $ipAddress);

if ($existingVote) {
    return ['success' => false, 'message' => 'IP has already voted'];
}

// If no existing vote, store new vote
$voteId = $this->storeVote($pollId, $optionId, $ipAddress);
```

---

## Vote Workflow

### Voting Process
1. User selects poll option (AJAX)
2. Get client IP address
3. Check if IP already voted
4. If not, store vote with is_active = TRUE
5. Log action to vote_history
6. Return results to frontend

### Vote Release Process (Admin)
1. Admin clicks "Release" on a voter
2. Mark vote as_active = FALSE
3. Log release action to vote_history
4. IP can now vote again

### Revote Process
1. IP submits new vote after release
2. Verify no active vote exists
3. Store new vote with is_active = TRUE
4. Log revote action with previous vote info

---

## Troubleshooting

### Database Connection Issues
- Check `bootstrap/app.php` for correct credentials
- Ensure MySQL is running
- Verify user has permissions

### Routes Not Working
- Check Apache mod_rewrite is enabled
- Verify .htaccess in public folder
- Check web server configuration

### AJAX Not Working
- Open browser console for JavaScript errors
- Check network tab in DevTools
- Verify CSRF token is being sent

### IP Restriction Not Working
- Check IP detection in VoteController.php
- Verify database entries
- Check vote_history logs

---

## Example Usage

### Creating a Poll (Admin)
1. Login with admin account
2. Go to Admin Panel  Create Poll
3. Enter question and options
4. Click Create Poll

### Voting
1. Login with any account
2. Select poll from dashboard
3. Choose option
4. Submit vote
5. View real-time results

### Managing Votes (Admin)
1. Go to Admin Dashboard
2. Select poll
3. View voters list
4. Click Release to allow revote
5. Check vote history

---

## Performance Optimization

- Database indexes on poll_id, ip_address, is_active
- AJAX polling instead of WebSockets for simplicity
- CSS animations instead of JavaScript
- Lazy loading of poll options
- Query optimization with proper joins

---

## Dependencies

- **jQuery**: AJAX functionality
- **Bootstrap 5**: Responsive UI framework
- **Three.js**: 3D effects (optional, used for visual enhancements)
- **PHP PDO**: Database abstraction
- **MySQL**: Database

---

## Future Enhancements

- WebSocket support for truly real-time updates
- Multiple IP types (IPv6 validation)
- Vote encryption
- Advanced analytics dashboard
- Email notifications
- Multi-language support
- Dark mode toggle

---

##  License

This project is created for educational purposes.

---

##  Developer Notes

### Code Standards
- PSR-4 autoloading
- Consistent naming conventions
- Proper error handling
- Security-first approach

### Testing
- Test with different browsers
- Test on different devices
- Test SQL injection attempts
- Test CSRF protection

### Security Checklist
-  All inputs validated and sanitized
-  All queries use prepared statements
-  CSRF tokens on all forms
-  Rate limiting recommended (not implemented)
-  HTTPS recommended for production

---

##  Support

For issues or questions, refer to:
1. Database schema in `database/schema.sql`
2. VotingEngine logic in `app/Core/VotingEngine.php`
3. API endpoints in `routes/web.php`
4. View templates in `resources/views/`

---

**Built with  using Laravel, PHP, MySQL, and AJAX**
