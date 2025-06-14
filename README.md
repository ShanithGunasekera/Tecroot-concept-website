# Tecroot Concept Website

This is a test website developed for the company **Tecroot**. The purpose of the project is to simulate a full-featured business management platform that includes:

- ✅ Inventory Management  
- 👥 Customer Tracking  
- 🧑‍💼 Employee Management  
- 📢 Advertisements  
- 🛒 Product Sales System

---

## 🌐 Tech Stack

- **Frontend**: HTML, CSS, JavaScript  
- **Backend**: PHP  
- **Database**: MySQL  
- **Local Server**: XAMPP  

---

## 👤 My Role – Employee Management

I was responsible for implementing the **Employee Management** section of the system, which includes:

- Adding new employees (name, role, salary)
- Editing employee details
- Deleting employees
- Searching employees
- Generating salary reports

---

## 🖥️ Running the Website Locally

This website is a conceptual project and is not hosted online. To run and test the website on your local machine, follow these steps:

### Prerequisites

- Install [XAMPP](https://www.apachefriends.org/index.html), which includes Apache, PHP, and MySQL.

### Steps

1. Start the Apache and MySQL modules in the XAMPP Control Panel.

2. Copy the entire project folder (`Tecroot`) into the `htdocs` directory of your XAMPP installation.  
   For example: `C:\xampp\htdocs\Tecroot`

3. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin) in your browser.

4. Create a new database (e.g., `tecroot_db`).

5. Import the `tecrootsql.sql` file from the project folder into the new database using the **Import** tab in phpMyAdmin.

6. Update the database connection settings in your project’s config file (e.g., `db_config.php`):

    $servername = "localhost";
    $username = "root";
    $password = "";  // Default XAMPP MySQL password is empty
    $dbname = "tecroot_db";  // The name of the database you created

7. Visit `http://localhost/Tecroot/` in your browser to access the website.

---

### Notes

- Ensure Apache and MySQL are running before accessing the site.
- This setup is for local development and testing only.

---
