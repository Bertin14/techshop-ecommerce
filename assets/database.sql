CREATE DATABASE IF NOT EXISTS techshop;
USE techshop;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT DEFAULT 0,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(20),
  address TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT,
  total_amount DECIMAL(10,2),
  status VARCHAR(50) DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id)
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  quantity INT,
  price DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO categories (name) VALUES ('Laptops'), ('Phones'), ('Accessories'), ('TVs'), ('Audio');

INSERT INTO products (category_id, name, description, price, stock, image) VALUES
(1, 'HP Laptop 15', '15.6" Intel Core i5, 8GB RAM, 256GB SSD', 750000, 10, 'hp-laptop.jpg'),
(1, 'Lenovo IdeaPad', '14" AMD Ryzen 5, 16GB RAM, 512GB SSD', 850000, 8, 'lenovo.jpg'),
(2, 'Samsung Galaxy A54', '6.4" AMOLED, 128GB, 5000mAh', 420000, 15, 'samsung-a54.jpg'),
(2, 'iPhone 13', '6.1" Super Retina, 128GB', 1100000, 5, 'iphone13.jpg'),
(3, 'Wireless Mouse', 'Ergonomic 2.4GHz wireless mouse', 15000, 50, 'mouse.jpg'),
(3, 'USB-C Hub', '7-in-1 USB-C hub with HDMI', 25000, 30, 'usb-hub.jpg'),
(4, 'LG 43" Smart TV', '4K UHD Android TV', 650000, 7, 'lg-tv.jpg'),
(5, 'Sony WH-1000XM4', 'Noise cancelling headphones', 280000, 12, 'sony-headphones.jpg');