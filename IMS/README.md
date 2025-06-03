# West Wild Inventory Management System

A comprehensive inventory management system for West Wild, built with PHP, MySQL, and modern web technologies.

## Features

- 📦 Inventory Management (add, edit, delete, and view items)
- 🗂️ Category Management
- 👤 User Profile & Admin Settings
- 🔄 Issue & Return Items
- 📊 Stock Reports with Filters
- 🔍 Search and Filter
- 📤 Print-Friendly Reports (and CSV export for Excel)
- 🔔 Stock Alerts (low stock warnings)
- 🔒 User Authentication (admin & user roles)

## Tech Stack

- Backend: PHP 8.x
- Database: MySQL
- Frontend: Bootstrap 5, jQuery, FontAwesome

## Installation

1. **Clone or copy the repository** to your local server directory (e.g., `htdocs` for XAMPP).
2. **Import the database schema**:
   - Open phpMyAdmin or your MySQL tool.
   - Import `database/schema.sql` to create the database and tables.
3. **Configure the database connection**:
   - Edit `config/database.php` if your MySQL username/password is not `root`/blank.
   - Default database name: `west_wild_ims`.
4. **Start your local server** (e.g., XAMPP, WAMP).
5. **Access the application** through your web browser at `http://localhost/IMS` (or your chosen folder).

## Default Admin Login
- **Username:** `admin`
- **Password:** `admin123`

## Directory Structure

```
IMS/
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── config/
├── database/
├── includes/
├── pages/
└── vendor/ (not required unless you add libraries)
```

## Usage Notes

### Reports: Print & Export
- To **print** a report, use the blue **Print Report** button on the Reports page. This opens a print-friendly version of the table (use your browser's Print or Save as PDF).
- To **export to Excel/CSV**, use the green **Export Items to Excel** button. This downloads a CSV file, which you can open in Excel. (Native .xlsx export is not included by default.)

### Features Overview
- **Dashboard:** Quick stats and recent activity.
- **Add/View Items:** Manage inventory items, including stock levels and categories.
- **Issue/Return Items:** Track item movement in and out.
- **Manage Categories:** Organize items by category.
- **Reports:** Filter, print, and export inventory data.
- **Profile:** Update your user info and password.
- **Admin Settings:** (Admins only) Configure system-wide settings.

## Customization
- Edit `assets/css/style.css` for custom styles.
- All main features are in the `pages/` directory.

## License

This project is proprietary software owned by West Wild. 