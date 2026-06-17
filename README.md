# techshop-ecommerce

My final exam for E_Commerce and Web Application

# ⚡ TechShop Rwanda — Electronics E-Commerce Store

![TechShop Banner](https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=1200&h=300&fit=crop)

> A full-stack e-commerce web application for electronics built with PHP, MySQL, Docker, and deployed on Railway. Features Mobile Money payment simulation, admin dashboard, and dark mode.

[![CI/CD](https://github.com/Bertin14/techshop-ecommerce/actions/workflows/ci.yml/badge.svg)](https://github.com/Bertin14/techshop-ecommerce/actions)
[![Live Demo](https://img.shields.io/badge/Live-Demo-brightgreen)](https://techshop-ecommerce-production.up.railway.app)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue)](https://www.docker.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1-purple)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)](https://www.mysql.com/)
[![Railway](https://img.shields.io/badge/Deployed-Railway-blueviolet)](https://railway.app)

---

## 🌐 Live Links

| Link               | URL                                                                      |
| ------------------ | ------------------------------------------------------------------------ |
| 🛍️ Store           | https://techshop-ecommerce-production.up.railway.app                     |
| 📊 Admin Dashboard | https://techshop-ecommerce-production.up.railway.app/admin/dashboard.php |
| 🔐 Admin Login     | https://techshop-ecommerce-production.up.railway.app/admin/login.php     |
| 💻 GitHub Repo     | https://github.com/Bertin14/techshop-ecommerce                           |

---

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Docker Setup](#docker-setup)
- [Database Schema](#database-schema)
- [Payment System](#payment-system)
- [Admin Panel](#admin-panel)
- [CI/CD Pipeline](#cicd-pipeline)

---

## ✨ Features

### 🛍️ Core E-Commerce

- 📦 Product listing with grid layout and category filters
- 🔍 Real-time search — filters products as you type
- 📄 Product detail pages with images, stock status, and descriptions
- 🛒 Shopping cart — add, update quantity, remove items
- 💳 Checkout with customer details and Mobile Money payment
- ✅ Order confirmation with full order summary

### 🎨 UI/UX

- 🌙 Dark/Light mode toggle with localStorage persistence
- 📱 Fully mobile responsive for all screen sizes
- ✨ Smooth card fade-in animations on page load
- 🏷️ NEW / HOT / SALE product badges
- ⭐ Star ratings on every product card
- 🔔 Toast notifications for user feedback
- 📊 Animated statistics counter on homepage
- 🦶 Professional multi-column footer

### 💳 Mobile Money Payment

- 📱 MTN Mobile Money with USSD code display
- 📱 Airtel Money with USSD code display
- 💵 Cash on Delivery option
- 🔄 Payment processing animation with progress bar
- 🎫 Auto-generated transaction IDs for simulation

### 📊 Admin Dashboard

- 🔐 Secure login with hashed password authentication
- 📦 Total products, orders, customers, revenue stat cards
- 🍩 Sales by Category donut chart (Chart.js)
- 📊 Top Selling Products bar chart
- 🕐 Recent orders table with status badges
- 🚪 Logout functionality

### ⚙️ DevOps

- 🐳 Docker + Docker Compose containerization
- 🔄 GitHub Actions CI/CD pipeline
- ☁️ Deployed on Railway cloud platform
- 🔐 Secure environment variable configuration
- 🗄️ Managed MySQL database on Railway

---

## 🛠️ Tech Stack

| Layer            | Technology              | Purpose                |
| ---------------- | ----------------------- | ---------------------- |
| Frontend         | HTML5, CSS3, JavaScript | UI and interactions    |
| Backend          | PHP 8.1                 | Server-side logic      |
| Database         | MySQL 8.0               | Data storage           |
| Charts           | Chart.js 4.4            | Admin visualizations   |
| Icons            | Font Awesome 6.5        | UI icons               |
| Containerization | Docker + Compose        | App packaging          |
| CI/CD            | GitHub Actions          | Automated testing      |
| Deployment       | Railway Cloud           | Live hosting           |
| Version Control  | Git + GitHub            | Source code management |

---

## 📁 Project Structure

```
techshop/
├── .github/
│   └── workflows/
│       └── ci.yml              # GitHub Actions CI/CD pipeline
├── admin/
│   ├── dashboard.php           # Admin analytics dashboard
│   ├── login.php               # Admin authentication
│   └── logout.php              # Session destroy + redirect
├── assets/
│   ├── css/
│   │   └── style.css           # All styles + dark mode + responsive
│   ├── js/
│   │   └── main.js             # JavaScript interactions
│   └── images/                 # Product images
├── includes/
│   ├── config.php              # Environment detection (XAMPP/Docker/Railway)
│   ├── db.php                  # Database connection (env variables)
│   ├── header.php              # Reusable header + navbar + dark mode
│   └── footer.php              # Reusable multi-column footer
├── index.php                   # Homepage with hero + stats + products
├── products.php                # Product listing + search + filter
├── product-detail.php          # Single product page + add to cart
├── cart.php                    # Shopping cart management
├── checkout.php                # Checkout + Mobile Money payment
├── order-confirmation.php      # Order success page
├── database.sql                # Database schema + seed data
├── Dockerfile                  # Docker image configuration
└── docker-compose.yml          # Multi-service Docker setup
```

---

## 🚀 Getting Started

### Prerequisites

- XAMPP (Apache + MySQL + PHP 8.1)
- Git
- Docker Desktop (optional)
- VS Code (recommended)

### Local Setup with XAMPP

1. **Clone the repository**

```bash
git clone https://github.com/Bertin14/techshop-ecommerce.git
```

2. **Move to XAMPP htdocs**

```
Copy project folder to: C:/xampp/htdocs/techshop
```

3. **Start XAMPP**

- Start Apache and MySQL in XAMPP Control Panel

4. **Create the database**

- Open http://localhost/phpmyadmin
- Create database named `techshop`
- Import `database.sql`

5. **Run the app**

```
http://localhost/techshop/index.php
```

---

## 🐳 Docker Setup

### Run with Docker Compose

```bash
# Build and start all services
docker compose up --build

# Access the app
http://localhost:8080
```

### Import database into Docker MySQL

```bash
# Open MySQL shell inside container
docker compose exec db mysql -u root -prootpassword

# Paste the SQL from database.sql and run
```

### Stop Docker

```bash
docker compose down
```

### Environment Variables

| Variable  | Description    | Default     |
| --------- | -------------- | ----------- |
| `DB_HOST` | MySQL host     | `localhost` |
| `DB_NAME` | Database name  | `techshop`  |
| `DB_USER` | MySQL username | `root`      |
| `DB_PASS` | MySQL password | ``          |

---

## 🗄️ Database Schema

```sql
categories     -- Product categories (Laptops, Phones, Accessories, TVs, Audio)
products       -- Product catalog with name, price, stock, image, category
customers      -- Customer info collected at checkout
orders         -- Order records with status and total amount
order_items    -- Individual line items for each order
admins         -- Admin users with hashed passwords
```

---

## 💳 Payment System

TechShop Rwanda simulates Mobile Money payments used in Rwanda:

| Method           | USSD Code              | Auto Transaction ID |
| ---------------- | ---------------------- | ------------------- |
| MTN Mobile Money | `*182*8*1*0788000000#` | TXN + random hash   |
| Airtel Money     | `*185*1*1*0733000000#` | AIR + random hash   |
| Cash on Delivery | —                      | Pay on delivery     |

> **Note:** This is a simulation for demonstration purposes. No real money is transferred.

---

## 🔐 Admin Panel

Access the admin panel at `/admin/login.php`

| Feature        | Description                          |
| -------------- | ------------------------------------ |
| Secure Login   | Username + bcrypt hashed password    |
| Stats Cards    | Products, Orders, Customers, Revenue |
| Sales Chart    | Donut chart by category              |
| Products Chart | Bar chart of top selling products    |
| Orders Table   | Recent orders with status            |

**Default credentials:**

- Username: `admin`
- Password: `password`

---

## 🔄 CI/CD Pipeline

Every push to `main` triggers the GitHub Actions workflow:

```yaml
Steps:
1. ✅ Checkout code from GitHub
2. 🐳 Set up Docker Buildx
3. 🔨 Build Docker image from Dockerfile
4. ✅ Validate PHP syntax on all .php files
5. 🚀 Run docker compose up to verify stack
6. 🛑 Clean up with docker compose down
```

---

## 👨‍💻 Author

**Rukundo Bertin** — Software Engineering Student at UNILAK

---

## 📄 Academic Information

- **Course:** EWA408510 — E-Commerce and Web Application
- **Institution:** University of UNILAK
- **Academic Year:** 2025-2026
- **Submission:** Final Examination Project

---

## 📜 License

This project was developed as a final examination project for EWA408510 at UNILAK. All rights reserved.
