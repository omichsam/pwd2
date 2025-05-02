# 🛠️ Project Setup Guide (XAMPP + PHPMyAdmin)

Follow these steps to set up and run the project locally using **XAMPP**:

---

## 📦 Prerequisites

- [Download and install XAMPP](https://www.apachefriends.org/index.html) (latest version recommended)

---

## 🚀 Setup Instructions

### 1. **Install and Launch XAMPP**

- Install XAMPP.
- Open the **XAMPP Control Panel**.
- Start **Apache** and **MySQL** services.

---

### 2. **Access PHPMyAdmin**

- In your browser, go to:  
  `http://localhost/phpmyadmin`

---

### 3. **Create the Database**

- Click on **"New"** in the left sidebar.
- Enter database name as:  
  `pwd`  
- Click **Create**.

---

### 4. **Import the Database**

- Select the `pwd` database.
- Click on the **"Import"** tab.
- Choose the `.sql` file located in the `project` folder.
- Click **Go** to import the database schema and data.

---

### 5. **Move the Project to htdocs**

- Locate your XAMPP installation directory (e.g., `C:\xampp`).
- Navigate to the `htdocs` folder.
- Copy your project folder (named `pwd1`) into the `htdocs` directory:
