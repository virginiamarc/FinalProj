  # 📊 Sales Records Management System  
A lightweight PHP/MySQL CRUD application built to demonstrate backend logic, database interaction, and clean UI structure. This project was developed as part of COP 2836 — Database‑Driven Web Programming.

---

## 🚀 Features

- Secure login system (session‑based authentication)
- Add, edit, view, and delete sales records
- MySQL database integration
- Clean Bootstrap‑based UI
- Pagination for large datasets
- Modular file structure (`config/`, `inc/`, etc.)
- Example configuration files for safe setup

---

## 🛠️ Tech Stack

- **PHP** (procedural)
- **MySQL / phpMyAdmin**
- **Bootstrap 4**
- **HTML5 / CSS3**
- **cPanel hosting environment**

---

## 📁 Project Structure

```
FinalProj/
│
├── config/
│   ├── config.example.php     # Safe example config
│   ├── db.example.php         # Safe example DB connection
│   ├── config.php             # Ignored (real credentials)
│   └── db.php                 # Ignored (real credentials)
│
├── inc/
│   ├── header.php
│   └── footer.php
│
├── css/
│   └── bootstrap.min.css
│
├── index.php
├── login.php
├── logout.php
├── add.php
├── edit.php
├── delete.php
├── view.php
└── README.md
```

---

## 🔐 Configuration

This repository **does not** include real database credentials.

To run the project locally:

1. Copy the example config files:
   ```
   config/config.example.php → config/config.php
   config/db.example.php → config/db.php
   ```

2. Update the new files with your own database credentials:
   ```php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'your_username');
   define('DB_PASSWORD', 'your_password');
   define('DB_NAME', 'your_database');
   ```

3. Import your SQL schema into MySQL.

4. Run the project on a PHP‑enabled server (XAMPP, WAMP, cPanel, etc.).

---

## 🌐 Live Demo

You can view the deployed version here:

👉 **[https://virginiamarc.com/FinalProj](https://virginiamarc.com/FinalProj)**

---

## 📸 Screenshots

_Add screenshots here once you upload them to the repo._

Example:

```
/screenshots/
    login.png
    dashboard.png
    table-view.png
```

---

## 🎯 Purpose

This project demonstrates:

- Backend development fundamentals  
- Database CRUD operations  
- Secure session handling  
- Clean UI structure  
- Real‑world deployment on cPanel  

It serves as a portfolio piece showcasing practical PHP/MySQL skills.

---

## 👩‍💻 Author

**Virginia Marc**  
Portfolio: [https://portfolio.virginiamarc.com](https://portfolio.virginiamarc.com)  
GitHub: [https://github.com/virginiamarc](https://github.com/virginiamarc)
