# 🌟 Leave Management System | HR Automation Platform

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/) 
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/) 
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML) 
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS) 
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript) 
[![XAMPP](https://img.shields.io/badge/XAMPP-ff8c00?style=for-the-badge)](https://www.apachefriends.org/) 
[![License](https://img.shields.io/badge/License-Open%20Source-green?style=for-the-badge)](#)

---

## 🚀 Project Overview

**Leave Management System** is a **modern, full-stack HR automation platform** that streamlines employee leave management for organizations.  

It empowers:  

- **Employees** to apply, track, and manage their leave requests effortlessly.  
- **Administrators** to approve, reject, and maintain leave records efficiently.  

> This system reduces manual work, increases transparency, and brings HR operations into the **digital age**.

---

## 🎯 Key Features

### 👨‍💼 Employee Panel
- Apply for leave with start & end dates  
- Track leave status in real-time  
- View leave history and remaining balance  
- Upload supporting documents  
- Secure login and password recovery (OTP-based)  

### 🛠 Admin Panel
- Approve / reject leave requests  
- Manage leave types & employee records  
- Department management  
- Export leave reports as **CSV**  
- Automated notifications via **SMTP**

### 📩 Smart Notifications
- Email notifications for approvals/rejections  
- SMTP integration using **PHPMailer**  
- OTP-based password reset and verification  

---

## 🛠 Tech Stack

### Backend
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)  
![PHPMailer](https://img.shields.io/badge/PHPMailer-ff69b4?style=for-the-badge)

### Database
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

### Frontend
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)  
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)  
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

### Tools & Deployment
![XAMPP](https://img.shields.io/badge/XAMPP-ff8c00?style=for-the-badge)  

---

## ⚙ Installation

1. **Clone the repository:**

```bash
git clone https://github.com/joyswapnilrajparadeshi-cmd/leave-management-system.git

Import Database:
Import leave_app.sql into MySQL.

Update DB Configuration:
Edit database credentials in db_connection.php (or your config file).

Start Server:
Run XAMPP/WAMP/LAMP and start Apache & MySQL.

Access the App:
Open in browser:
http://localhost/leave-management-system/

📁 Project Structure
leave-management-system/
├── PHPMailer-master/       # Email library
├── admin_dashboard.php
├── cancel_leave.php
├── delete_leave.php
├── department.php
├── edit_leave.php
├── employee_panel/        # Employee-facing files
├── export_csv.php
├── forgot_password.php
├── index.php
├── leave_app.sql
├── leave_types.php
├── login.php
├── logout.php
├── process_login.php
├── process_register.php
├── register.php
├── reset_password.php
├── smtp_test.php
├── style.css
├── submit_leave.php
├── update_leave.php
├── update_leave_status.php
├── user_dashboard.php
├── verify_otp.php
└── README.md
💡 Future Enhancements

AI-driven leave prediction & analytics

Mobile-responsive UI / Progressive Web App

Cloud deployment with secure authentication

Multi-organization support

👨‍💻 Author

Paradeshi Joy Swapnil Raj
B.Tech CSE | Full Stack Developer | AI & ML Enthusiast

📧 joyswapnilrajparadeshi@gmail.com

🌐 Portfolio
