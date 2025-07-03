# 🗂️ PHP CRUD App with Login & File Upload

A web application built using **PHP**, **MySQL**, and **Bootstrap**. This app allows users to **log in**, **store text entries**, and **upload files**. It’s ideal for learning core backend development concepts like authentication, CRUD operations, and file handling.

---

## 🚀 Features

- 🔐 User Authentication (Login / Logout)
- ✏️ Create, Read, Update, Delete (CRUD) operations
- 📎 Upload and manage files with text entries
- 🧰 MySQL database integration
- 🎨 Responsive design using Bootstrap 5

---

## 📁 File Structure

crud_app/
│
├── 🏠 index.php                 ← Entry point (login)
├── 🔐 auth/
│   ├── 🔑 login.php             ← Processes login
│   ├── 🚪 logout.php             ← Handles logout
│   ├── 📝 register-form.php     ← Register form
│   └── ✅ register.php          ← Processes registration
├── 📋 dashboard.php             ← Post-login CRUD page
├── 🎨 assets/
│   ├── 🎨 css/
│   │   ├── 🎨 auth.css
│   │   └── 📋 dashboard.css      ← Dashboard styling
│   └── 🖼️ images/
│       ├── 🖼️ logo.png
│       └── 🌟 favicon.png
├── 🔧 includes/
│   └── 🛢️ db.php                ← Database connection
├── 📄 README.md
└── 🚫 .gitignore

---
