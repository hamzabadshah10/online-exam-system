<div align="center">

<img src="https://capsule-render.vercel.app/api?type=waving&color=gradient&customColorList=6,11,20&height=200&section=header&text=EduQuest%20Online%20Exam&fontSize=60&fontColor=ffffff&animation=fadeIn&fontAlignY=38&desc=Web%20Development%20%7C%20PHP%20%7C%20MySQL&descAlignY=60&descAlign=50" width="100%"/>

<br/>

# 🎓 EduQuest: Online Examination System

### *A professional, secure, and intuitive digital assessment platform.*

<br/>

[![PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892bf.svg?style=for-the-badge&logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)

<br/>

[![GitHub Stars](https://img.shields.io/github/stars/hamzabadshah10/online-exam-system?style=for-the-badge&logo=github&color=ffd700)](https://github.com/hamzabadshah10/online-exam-system/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/hamzabadshah10/online-exam-system?style=for-the-badge&logo=github&color=4fc3f7)](https://github.com/hamzabadshah10/online-exam-system/network)
[![GitHub Issues](https://img.shields.io/github/issues/hamzabadshah10/online-exam-system?style=for-the-badge&logo=github&color=ff7043)](https://github.com/hamzabadshah10/online-exam-system/issues)
[![License](https://img.shields.io/badge/License-MIT-22c55e?style=for-the-badge)](LICENSE)

</div>

---

## 👩‍💻 About This Repository

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

<div align="center">
  <img width="50%" alt="EduQuest Preview" src="https://github.com/user-attachments/assets/61f166f9-ac29-43a9-aea1-f24c95ad2cf7" />
</div>

---

## 🧠 Skills & Technologies

<div align="center">

### Languages & Core Tools
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-323330?style=flat-square&logo=javascript&logoColor=F7DF1E)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)

### Frameworks & Libraries
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)
![Git](https://img.shields.io/badge/Git-F05032?style=flat-square&logo=git&logoColor=white)

### Concepts Covered
![Web Development](https://img.shields.io/badge/Web%20Development-6f42c1?style=flat-square)
![Database Management](https://img.shields.io/badge/Database%20Management-6f42c1?style=flat-square)
![Security](https://img.shields.io/badge/Security-6f42c1?style=flat-square)

</div>

---

## 🚀 Quick Start

Follow these steps to deploy EduQuest locally:

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
```bash
# Clone the repository into your htdocs/www folder
git clone https://github.com/hamzabadshah10/online-exam-system.git
```
- Start Apache and MySQL via your control panel.
- Access the system at `http://localhost/online-exam-system/`.

---

## 🤝 Connect & Contribute

<div align="center">

[![GitHub Follow](https://img.shields.io/github/followers/hamzabadshah10?label=Follow%20on%20GitHub&style=for-the-badge&logo=github&color=181717)](https://github.com/hamzabadshah10)
[![Star Repo](https://img.shields.io/badge/⭐%20Star%20This%20Repo-ffd700?style=for-the-badge)](https://github.com/hamzabadshah10/online-exam-system/stargazers)
[![Fork Repo](https://img.shields.io/badge/🍴%20Fork%20This%20Repo-4fc3f7?style=for-the-badge)](https://github.com/hamzabadshah10/online-exam-system/fork)

</div>

Feel free to explore, fork, or reach out with any questions! If you found this repo helpful, please **⭐ star it** — it means a lot! 🙏

---

## 📄 License

Distributed under the **MIT License** — free to use, share, and adapt with attribution.

---

<div align="center">

### 👩‍💻 Author

**Hamza Badshah**
*Software Engineer | Pak-Austria Fachhochschule Institute of Applied Sciences and Technology*

[![GitHub](https://img.shields.io/badge/GitHub-hamzabadshah10-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/hamzabadshah10)

<br/>

*Developed with ❤️ for Academic Excellence.*

<br/>

<img src="https://capsule-render.vercel.app/api?type=waving&color=gradient&customColorList=6,11,20&height=100&section=footer" width="100%"/>

</div>
