# 8BitBrain - Quiz Website Project

A full-stack quiz website with MySQL database integration, admin dashboard, and user management system.

## 🎮 Project Overview

8BitBrain is a retro-themed quiz platform featuring:
- User authentication (Login/Signup)
- Admin dashboard with CRUD operations
- Multiple game modes
- Leaderboards
- MySQL database integration
- Responsive design

## 📁 File Structure

```
8bitbrain_project/
│
├── index.html              # Homepage
├── about.html              # About page
├── login.html              # Login page
├── signup.html             # Signup page
├── dashboard-admin.html    # Admin dashboard
├── dashboard-user.html     # User dashboard
├── modes.html              # Game modes
├── contacts.html           # Contact page
├── leaderboards.html       # Leaderboards
│
├── style.css               # All styles
├── script.js               # Main JavaScript
├── admin-dashboard.js      # Admin CRUD operations
│
├── db.php                  # Database connection
│
├── api/                    # PHP API endpoints
│   ├── get_users.php
│   ├── create_user.php
│   ├── update_user.php
│   ├── delete_user.php
│   ├── get_quizzes.php
│   ├── get_feedback.php
│   └── get_stats.php
│
├── 8bitbrain_db.sql        # Original database
├── 8bitbrain_new_tables_only.sql  # New tables only
│
├── sample-quizzes.json     # Sample quiz data
│
├── MYSQL-INTEGRATION-GUIDE.txt    # Setup guide
└── README.md               # This file
```

## 🚀 Setup Instructions

### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP
3. Start Apache and MySQL

### Step 2: Setup Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "Import" tab
3. Choose file: `8bitbrain_new_tables_only.sql`
4. Click "Import"
5. ✅ Database created with 8 tables

### Step 3: Place Files
1. Copy entire `8bitbrain_project` folder to:
   ```
   C:/xampp/htdocs/8bitbrain/
   ```

2. Your structure should be:
   ```
   C:/xampp/htdocs/8bitbrain/
   ├── index.html
   ├── db.php
   ├── api/
   └── (all other files)
   ```

### Step 4: Access Website
- Homepage: http://localhost/8bitbrain/index.html
- Admin Dashboard: http://localhost/8bitbrain/dashboard-admin.html
- Login: http://localhost/8bitbrain/login.html

## 🔑 Default Admin Login

```
Username: admin
Email: admin@8bitbrain.com
Password: admin123
```

⚠️ **Change this password after first login!**

## 📊 Database Tables

1. **users** - User accounts
2. **quizzes** - Quiz information
3. **questions** - Quiz questions
4. **answers** - Answer options
5. **quiz_attempts** - User quiz history
6. **leaderboards** - Game rankings
7. **feedback** - User feedback
8. **quiz_references** - Quiz references

## ✨ Features

### Admin Dashboard
- ✅ User Management (CRUD)
- ✅ Quiz Management
- ✅ Feedback Management
- ✅ Statistics Dashboard
- ✅ Search functionality

### User Features
- ✅ Registration & Login
- ✅ Multiple game modes
- ✅ Leaderboards
- ✅ Quiz taking
- ✅ Feedback submission

## 🛠️ Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Server:** Apache (XAMPP)

## 📝 API Endpoints

### GET Requests
- `api/get_users.php` - Fetch all users
- `api/get_quizzes.php` - Fetch all quizzes
- `api/get_feedback.php` - Fetch all feedback
- `api/get_stats.php` - Dashboard statistics

### POST Requests
- `api/create_user.php` - Create new user
- `api/update_user.php` - Update user
- `api/delete_user.php` - Delete user

## 👥 Team

- Hanz Christian Galgana - Front-end Developer
- Karl Lawrence Pacia - Back-end Developer
- Luis Magluyan Garcia - UI/UX Designer
- Lance Jasper Porciuncula - QA Engineer
- Angela Nichole Bairan - Database Administrator

---

**Made with ❤️ by the 8BitBrain Team**

Version: 1.0
Last Updated: March 2026
