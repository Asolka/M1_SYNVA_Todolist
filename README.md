# 🍃 Ma Tout Doux List

A web-based task manager with an Animal Crossing-inspired pastel theme.

## ✨ Features

- 📋 **Task list** — sortable table by title, category, priority, status, and dates
- ✏️ **Create / Edit / Delete** tasks with form validation
- 🏷️ **Categories, priorities, statuses** — each task is classified with colored badges
- 🎨 **Labels** — custom color-coded label system (many-to-many relationship)
- 📊 **Dashboard** — counters and Chart.js graphs (doughnut by status, bar charts by category and priority)
- 🔒 **Security** — random session codes to protect IDs, prepared SQL statements (mysqli)
- 📱 **Responsive** — mobile-friendly with horizontal scroll on the table

## 🛠️ Tech Stack

- **Backend** — PHP 8 / MySQL (mysqli)
- **Frontend** — HTML5, CSS3, Bootstrap 5, Chart.js
- **Server** — WAMP (Windows, Apache, MySQL, PHP)
- **Font** — Google Fonts (Quicksand)

## 📁 Project Structure

```
todolist/
├── assets/
│   ├── css/
│   │   └── style.css           # Animal Crossing pastel theme
│   ├── images/                 # Theme icons and images
│   └── js/
│       └── script.js
├── includes/
│   ├── connectDB.inc.php       # DB connection + security function
│   ├── header.inc.php          # HTML header + navbar
│   ├── footer.inc.php          # Footer + CDN scripts
│   └── auth.php                # DB credentials (not versioned)
├── sql/
│   └── todolist.sql            # Database creation script
├── todolist.php                # Home page (task list)
├── taskForm.php                # Create / edit form
├── saveTask.php                # Form processing
├── deleteTask.php              # Task deletion
└── dashboard.php               # Dashboard with charts
```

## 🚀 Installation

1. Install WAMP (or equivalent)
2. Clone the project into the `www/` folder
3. Import `sql/todolist.sql` into phpMyAdmin
4. Create the file `includes/auth.php` with your DB credentials:
   ```php
   <?php
   $host = "localhost";
   $user = "root";
   $passwd = "";
   $db = "todolist";
   ?>
   ```
5. Go to `http://localhost/todolist/todolist.php`

## 🗄️ Database

6 tables: `tache`, `categorie`, `priorite`, `statut`, `etiquette`, `est_marquee_par` (many-to-many between tasks and labels).

## 🎓 Project

M1 SYNVA 2025-2026
