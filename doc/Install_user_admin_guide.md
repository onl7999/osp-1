# INSTALLATION INSTRUCTIONS
## Requirements

Make sure your system has the following installed:

- Git
- PHP
- MariaDB
- Apache2
- PHP MySQL extension (`pdo_mysql`)

---

### 1. Install Apache2

```bash
sudo apt update
sudo apt install apache2
```

### 2. Install MariaDB

```bash
sudo apt install mariadb-server
```

### 3. Install PHP + Apache Integration

```bash
sudo apt install php libapache2-mod-php
sudo systemctl restart apache2
```

### 4. Install PHP MySQL (PDO) Extension

```bash
sudo apt install php-mysql
```

### 5. Install Git

```bash
sudo apt install git
```

---

## Set Up the Project

Navigate to the web server root directory:

```bash
cd /var/www/html
```

Clone the repository:

```bash
git clone https://github.com/onl7999/osp-1/main
```

Move the contents to the HTML root:

```bash
sudo cp -r osp-1/. /var/www/html/
sudo rm -rf osp-1
```

---

## Find  Raspberry Pi's IP

```bash
hostname -I
```

Open a browser and visit:  
`http://<your-ip-address>`

---

## Database Configuration

### 1. Open the MariaDB Shell

```bash
sudo mariadb -u root
```

### 2. Set a Password for Root (Optional)

```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'yourpassword';
FLUSH PRIVILEGES;
EXIT;
```

Update your password in:

```bash
/var/www/html/fn/db.php
```

### 3. Create Database and Tables

Reopen the MariaDB shell:

```bash
sudo mariadb -u root -p
```

Paste the following SQL to set up the schema (empty tables):

```sql
CREATE DATABASE IF NOT EXISTS expense_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE expense_tracker;

CREATE TABLE categories (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE expenses (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  category_id INT(11) DEFAULT NULL,
  amount DECIMAL(10,2) NOT NULL,
  description TEXT DEFAULT NULL,
  expense_date DATE NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY category_id (category_id),
  CONSTRAINT expenses_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT expenses_ibfk_2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Type `exit` to leave the MariaDB shell.

---

## Done

Open your browser and go to your Pi’s IP address (from `hostname -I`).

---

## Troubleshooting

**Error: “Could not find driver”**

Fix: Install the PHP MySQL extension:

```bash
sudo apt install php-mysql
```

Restart Apache if needed:

```bash
sudo systemctl restart apache2
```
