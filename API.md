# API Documentation - Real-Time Poll Platform

Complete API reference with request/response examples.

---

## Base URL
```
http://polling.local
```

## Authentication
All endpoints (except `/login`) require an authenticated session via `$_SESSION['user_id']`.

---

## Authentication Endpoints

### 1. Login
**Endpoint**: `POST /login`

**Description**: Authenticate user with email and password

**Request**:
```
Method: POST
Content-Type: application/x-www-form-urlencoded

Parameters:
- email (required): User email
- password (required): User password
- csrf_token (required): CSRF protection token
```

**Response (Success)**:
```
Redirect to: /dashboard
Session created with user_id, user_name, user_role
```

**Response (Error)**:
```
Redirect to: /
Session message: "Invalid email or password"
```

**Example**:
```bash
curl -X POST http://polling.local/login \
  -d "email=admin@polling.test" \
  -d "password=admin123" \
  -d "csrf_token=..." \
  -c cookies.txt
```

---

### 2. Logout
**Endpoint**: `GET /logout`

**Description**: Destroy user session

**Request**:
```
Method: GET
```

**Response**:
```
Redirect to: /
Session destroyed
```

---

## Poll Endpoints

### 1. Get All Active Polls
**Endpoint**: `GET /api/polls`

**Description**: Fetch all active polls

**Response (Success)**:
```json
{
  "success": true,
  "polls": [
    {
      "id": 1,
      "question": "What is your favorite programming language?",
      "status": "active",
      "created_by": 1,
      "created_at": "2026-04-15 10:00:00",
      "updated_at": "2026-04-15 10:00:00"
    }
  ]
}
```

**Response (Error)**:
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**HTTP Status**: 
- 200: Success
- 401: Unauthorized

---

### 2. Get Single Poll with Options
**Endpoint**: `GET /api/polls/{pollId}`

**Description**: Fetch specific poll with all options

**Parameters**:
```
pollId (required): Integer poll ID
```

**Response (Success)**:
```json
{
  "success": true,
  "poll": {
    "id": 1,
    "question": "What is your favorite programming language?",
    "status": "active",
    "created_by": 1,
    "created_at": "2026-04-15 10:00:00",
    "options": [
      {
        "id": 1,
        "option_text": "PHP"
      },
      {
        "id": 2,
        "option_text": "Python"
      },
      {
        "id": 3,
        "option_text": "JavaScript"
      }
    ]
  }
}
```

**Response (Error)**:
```json
{
  "success": false,
  "message": "Poll not found"
}
```

**HTTP Status**: 
- 200: Success
- 404: Poll not found
- 401: Unauthorized

**Example**:
```bash
curl -X GET http://polling.local/api/polls/1 \
  -H "X-Requested-With: XMLHttpRequest" \
  -b cookies.txt
```

---

## Voting Endpoints

### 1. Cast Vote
**Endpoint**: `POST /api/vote/cast`

**Description**: Submit a vote for a poll option

**Request**:
```
Method: POST
Content-Type: application/json

Body:
{
  "poll_id": 1,
  "option_id": 2
}
```

**Response (Success)**:
```json
{
  "success": true,
  "message": "Vote recorded successfully",
  "vote_id": 42,
  "ip_address": "192.168.1.100"
}
```

**Response (Error)**:
```json
{
  "success": false,
  "message": "IP has already voted on this poll"
}
```

**Possible Errors**:
- "IP has already voted on this poll" (Status 400)
- "Poll not found" (Status 400)
- "Poll is inactive" (Status 400)
- "Invalid option for this poll" (Status 400)

**HTTP Status**: 
- 200: Success
- 400: Validation error
- 401: Unauthorized
- 405: Method not allowed

**Example**:
```javascript
$.ajax({
  url: '/api/vote/cast',
  method: 'POST',
  contentType: 'application/json',
  data: JSON.stringify({
    poll_id: 1,
    option_id: 2
  }),
  success: function(response) {
    console.log('Vote cast:', response);
  }
});
```

---

### 2. Check Vote Status
**Endpoint**: `GET /api/vote/status`

**Description**: Check if current IP has voted on a poll

**Parameters**:
```
poll_id (required): Integer poll ID
```

**Response (Success)**:
```json
{
  "success": true,
  "hasVoted": true,
  "optionId": 2
}
```

**Response (Not Voted)**:
```json
{
  "success": true,
  "hasVoted": false,
  "optionId": null
}
```

**HTTP Status**: 200

**Example**:
```bash
curl -X GET "http://polling.local/api/vote/status?poll_id=1" \
  -H "X-Requested-With: XMLHttpRequest" \
  -b cookies.txt
```

---

### 3. Get Poll Results
**Endpoint**: `GET /api/results`

**Description**: Get current poll results with vote counts and percentages

**Parameters**:
```
poll_id (required): Integer poll ID
```

**Response (Success)**:
```json
{
  "success": true,
  "total_votes": 150,
  "options": [
    {
      "id": 1,
      "option_text": "PHP",
      "vote_count": 50,
      "percentage": 33.33
    },
    {
      "id": 2,
      "option_text": "Python",
      "vote_count": 60,
      "percentage": 40.0
    },
    {
      "id": 3,
      "option_text": "JavaScript",
      "vote_count": 40,
      "percentage": 26.67
    }
  ]
}
```

**HTTP Status**: 200

**Example**:
```bash
curl -X GET "http://polling.local/api/results?poll_id=1" \
  -H "X-Requested-With: XMLHttpRequest" \
  -c cookies.txt
```

---

## Admin Endpoints

### 1. Get Poll Voters
**Endpoint**: `GET /api/admin/voters`

**Description**: Get all voters for a specific poll (Admin only)

**Parameters**:
```
poll_id (required): Integer poll ID
```

**Response (Success)**:
```json
{
  "success": true,
  "voters": [
    {
      "ip_address": "192.168.1.100",
      "vote_id": 42,
      "option_text": "Python",
      "voted_at": "2026-04-15 10:30:00",
      "is_active": true,
      "last_action": "vote"
    },
    {
      "ip_address": "192.168.1.101",
      "vote_id": 43,
      "option_text": "PHP",
      "voted_at": "2026-04-15 10:32:00",
      "is_active": false,
      "last_action": "release"
    }
  ]
}
```

**Response (Error - Not Admin)**:
```
HTTP Status: 401
```

---

### 2. Release Vote by IP
**Endpoint**: `POST /api/admin/vote/release`

**Description**: Release a voter's vote to allow re-voting (Admin only)

**Request**:
```
Method: POST
Content-Type: application/json

Body:
{
  "poll_id": 1,
  "ip_address": "192.168.1.100"
}
```

**Response (Success)**:
```json
{
  "success": true,
  "message": "Vote released successfully",
  "ip_address": "192.168.1.100",
  "previous_option": 2
}
```

**Response (Error)**:
```json
{
  "success": false,
  "message": "No active vote found for this IP"
}
```

**HTTP Status**: 
- 200: Success
- 400: Error
- 401: Unauthorized

---

### 3. Get Vote History
**Endpoint**: `GET /api/admin/vote-history`

**Description**: Get complete vote history for a poll (Admin only)

**Parameters**:
```
poll_id (required): Integer poll ID
ip_address (optional): Specific IP address to filter
```

**Response (Success)**:
```json
{
  "success": true,
  "history": [
    {
      "id": 1,
      "poll_id": 1,
      "option_id": 2,
      "option_text": "Python",
      "ip_address": "192.168.1.100",
      "action_type": "vote",
      "timestamp": "2026-04-15 10:30:00",
      "details": "{\"vote_id\":42,\"timestamp\":\"2026-04-15 10:30:00\"}"
    },
    {
      "id": 2,
      "poll_id": 1,
      "option_id": null,
      "option_text": null,
      "ip_address": "192.168.1.100",
      "action_type": "release",
      "timestamp": "2026-04-15 10:35:00",
      "details": "{\"released_vote_id\":42,\"original_option_id\":2}"
    },
    {
      "id": 3,
      "poll_id": 1,
      "option_id": 1,
      "option_text": "PHP",
      "ip_address": "192.168.1.100",
      "action_type": "revote",
      "timestamp": "2026-04-15 10:36:00",
      "details": "{\"new_vote_id\":50,\"new_option_id\":1}"
    }
  ]
}
```

---

### 4. Toggle Poll Status
**Endpoint**: `POST /api/admin/poll/status`

**Description**: Activate or deactivate a poll (Admin only)

**Request**:
```
Method: POST
Content-Type: application/json

Body:
{
  "poll_id": 1,
  "status": "inactive"
}
```

**Response (Success)**:
```json
{
  "success": true,
  "message": "Poll status updated"
}
```

---

### 5. Delete Poll
**Endpoint**: `POST /api/admin/poll/delete`

**Description**: Permanently delete a poll (Admin only)

**Request**:
```
Method: POST
Content-Type: application/json

Body:
{
  "poll_id": 1
}
```

**Response (Success)**:
```json
{
  "success": true,
  "message": "Poll deleted"
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Invalid input"
}
```

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 405 Method Not Allowed
```json
{
  "success": false,
  "message": "Method not allowed"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "An error occurred"
}
```

---

## Common Headers

### Request Headers
```
Content-Type: application/json
X-Requested-With: XMLHttpRequest
Cookie: PHPSESSID=...
```

### Response Headers
```
Content-Type: application/json
Cache-Control: no-cache, no-store, must-revalidate
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
```

---

## Rate Limiting

Currently not implemented. For production, consider:
- Limit votes per IP per time period
- Limit API requests per session
- Implement exponential backoff for failed requests

---

## Data Types

| Type | Description | Examples |
|------|-------------|----------|
| integer | Whole number | 1, 42, 100 |
| string | Text | "PHP", "user@email.com" |
| boolean | True/False | true, false |
| enum | Fixed values | "active", "inactive", "admin", "user" |
| timestamp | Date-time | "2026-04-15 10:30:00" |
| json | JSON object/array | {"key": "value"} |

---

## Testing

### Using cURL
```bash
# Get all polls
curl -X GET http://polling.local/api/polls \
  -H "X-Requested-With: XMLHttpRequest" \
  -b cookies.txt

# Cast vote
curl -X POST http://polling.local/api/vote/cast \
  -H "Content-Type: application/json" \
  -d '{"poll_id":1,"option_id":2}' \
  -b cookies.txt

# Get results
curl -X GET "http://polling.local/api/results?poll_id=1" \
  -H "X-Requested-With: XMLHttpRequest" \
  -b cookies.txt
```

### Using Postman
1. Import requests as collection
2. Add cookies from login response
3. Test each endpoint

### Using JavaScript (AJAX)
```javascript
// Cast vote
$.ajax({
  url: '/api/vote/cast',
  type: 'POST',
  contentType: 'application/json',
  data: JSON.stringify({ poll_id: 1, option_id: 2 }),
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  },
  success: function(response) {
    console.log(response);
  },
  error: function(error) {
    console.error(error);
  }
});
```

---

## Changelog

### Version 1.0.0 (2026-04-15)
- Initial release
- Core voting functionality
- Admin panel
- Real-time results
- Vote history tracking
