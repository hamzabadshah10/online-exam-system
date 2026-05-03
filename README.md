# 🎓 EduQuest: Online Examination System

[![PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892bf.svg?style=for-the-badge&logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

**EduQuest** is a professional, high-fidelity examination ecosystem designed for elite institutions. It provides a secure, scalable, and intuitive platform for managing digital assessments, featuring advanced anti-cheat mechanisms and a premium user experience.

---

## 🚀 Key Features

### 👨‍🎓 Student Portal
- **Intuitive Onboarding:** Seamless registration and secure authentication.
- **Dynamic Exam Engine:** Real-time timer, question navigation, and status tracking.
- **Instant Analytics:** Detailed performance reports with rank calculation and answer review.
- **Mobile Responsive:** Study and take exams on any device.

### 👩‍💼 Admin Dashboard
- **Comprehensive Oversight:** Monitor total students, active exams, and overall registrations.
- **Exam Management:** Create, update, and delete examinations with ease.
- **Bulk Data Import:** Standardized CSV parser for large-scale question bank updates.
- **Live Monitoring:** Real-time tracking of ongoing examination sessions.

### 🛡️ Security & Integrity
- **Anti-Cheat System:** Automatic submission if students attempt to switch tabs or minimize the window.
- **Secure Persistence:** PDO-based database interactions with prepared statements.
- **Role-Based Access (RBAC):** Strict separation of student and administrative privileges.

---

## 📂 Project Structure

The project follows a clean, modular architecture for maximum maintainability:

| Directory | Description |
| :--- | :--- |
| `📂 php/` | Core backend logic (Login, Registration, Entry Points). |
| `📂 admin/` | Administrative dashboards and management tools. |
| `📂 student/` | Student-specific portals, exam engine, and result views. |
| `📂 api/` | Functional endpoints for database transactions (auth, exam logic). |
| `📂 config/` | Centralized database configuration and server settings. |
| `📂 html/` | UI Templates and legacy interface mockups. |
| `📂 database/` | SQL schemas and migration scripts. |
| `📂 assets/` | Static files (CSS, JS, Images). |

---

## 🛠️ Tech Stack

- **Frontend:** HTML5, Vanilla JavaScript, [Tailwind CSS](https://tailwindcss.com/) (CDN)
- **Backend:** PHP (>= 7.4)
- **Database:** MySQL
- **Typography:** Plus Jakarta Sans & Inter (Google Fonts)

---

## ⚙️ Installation Guide

### 1. Prerequisites
- **Web Server:** XAMPP, WAMP, or MAMP.
- **PHP:** Version 7.4 or higher.
- **Database:** MySQL.

### 2. Database Setup
1. Create a new database in your SQL manager (e.g., `eduquest_db`).
2. Import the schema from `database/database.sql`.
3. Configure your credentials in `config/db.php`:
   ```php
   $host = 'localhost';
   $db   = 'eduquest_db';
   $user = 'root';
   $pass = '';
   ```

### 3. Launching
1. Move the project folder to your server's root directory (`htdocs` or `www`).
2. Start Apache and MySQL via your control panel.
3. Access the system at `http://localhost/online_exam_system/`.

---

## 📜 License

Distributed under the MIT License. See `LICENSE` for more information.

---

<p align="center">
``` Developed with ❤️ for Academic Excellence.

Hamza Badshah |Software Engineer| Pak-Austria Fachhochschule Institute of Applied Sciences and Technology | Department of IT and CS```
</p>
