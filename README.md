# Multi-User Document Editor System

## Overview
This project is a collaborative document platform with role-based access, sharing permissions, version history, comments, folder organization, and activity logging.

## Completed Modules
- Backend API in PHP (all endpoints implemented)
- Frontend UI in HTML/CSS/JavaScript (all pages implemented)
- MySQL schema and sample data scripts

## Stack
- Frontend: HTML, CSS, Vanilla JavaScript
- Backend: PHP (PDO)
- Database: MySQL
- Local server: XAMPP or any PHP + MySQL setup

## Project Structure
- client/: frontend pages, styles, JavaScript
- server/: API endpoints and config
- database/: schema and sample data
- Schema.sql: coursework SQL reference

## Setup Guide
1. Create database schema:
	- Run database/schema.sql in MySQL.
2. Add sample records:
	- Run database/sample_data.sql.
3. Configure backend connection via environment variables (optional):
	- DB_HOST (default 127.0.0.1)
	- DB_PORT (default 3306)
	- DB_NAME (default multi_user_editor)
	- DB_USER (default root)
	- DB_PASS (default empty)
4. Host the repository in a PHP server root (for XAMPP, under htdocs).
5. Open client/index.html from the local server URL.

## One-Click Local Run
From the project root, run:

```bash
./run-local.sh
```

This starts a local PHP server at http://127.0.0.1:8080 and opens the app automatically.

## Default Test Login
If you loaded sample data, you can login with any sample email.
- Example emails:
  - ritesh@example.com
  - kajal@example.com
  - rudra@example.com
- Password hash is preloaded in sample data; you can also register a fresh account from the UI.

## API Endpoints
### User
- POST /server/api/user/register.php
- POST /server/api/user/login.php
- GET /server/api/user/get_users.php

### Document
- POST /server/api/document/create.php
- GET /server/api/document/get.php
- POST /server/api/document/update.php
- POST /server/api/document/delete.php
- GET /server/api/document/version.php

### Collaboration
- POST /server/api/collaboration/share.php
- POST /server/api/collaboration/permission.php

### Folder
- POST /server/api/folder/create.php
- GET /server/api/folder/get.php
- POST /server/api/folder/move.php

### Comment
- POST /server/api/comment/add.php
- GET /server/api/comment/get.php

### Activity
- POST /server/api/activity/log.php
- GET /server/api/activity/get.php

## Feature Coverage
- Registration and login
- Document create, read, update, delete
- Automatic version creation on content updates
- Share documents with View/Edit permission
- Comment on documents
- Create folders and move documents
- Activity tracking for major actions

## Notes
- Authentication is currently session-less and browser-local (localStorage user state).
- CORS is enabled in backend bootstrap for local development.