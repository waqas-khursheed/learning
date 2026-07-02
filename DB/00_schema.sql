-- ============================================================================
--  MASTER SCHEMA — "company_db"
--  Is poori MySQL course (DB/ folder) ke har file me YEHI schema use hoga.
--  Pehle isay apne MySQL me run kar lo, phir baqi files ke queries
--  copy-paste karke direct try kar sakte ho.
--
--  Maqsad: Real-world jaisa schema (departments -> employees -> manager
--  self-relation, customers -> orders -> products) taake JOIN, INDEX,
--  WINDOW FUNCTION, TRICKY QUERIES sab REAL example pe practice ho sakein.
-- ============================================================================

DROP DATABASE IF EXISTS company_db;
CREATE DATABASE company_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE company_db;

-- ----------------------------------------------------------------------------
-- 1) departments
-- ----------------------------------------------------------------------------
CREATE TABLE departments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    location    VARCHAR(100) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- 2) employees  (manager_id -> khud isi table ka self-reference, FK relation
--    aur recursive/self-join examples ke liye)
-- ----------------------------------------------------------------------------
CREATE TABLE employees (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    department_id   INT UNSIGNED,
    manager_id      INT UNSIGNED NULL,
    salary          DECIMAL(10,2) NOT NULL,
    hire_date       DATE NOT NULL,
    status          ENUM('active','inactive','terminated') DEFAULT 'active',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_emp_department FOREIGN KEY (department_id)
        REFERENCES departments(id) ON DELETE SET NULL ON UPDATE CASCADE,

    CONSTRAINT fk_emp_manager FOREIGN KEY (manager_id)
        REFERENCES employees(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- 3) customers
-- ----------------------------------------------------------------------------
CREATE TABLE customers (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    city        VARCHAR(100),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- 4) products
-- ----------------------------------------------------------------------------
CREATE TABLE products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    category    VARCHAR(80) NOT NULL,
    price       DECIMAL(10,2) NOT NULL,
    stock       INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- 5) orders  (customer -> order -> product, many-to-many real-world style)
-- ----------------------------------------------------------------------------
CREATE TABLE orders (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id  INT UNSIGNED NOT NULL,
    product_id   INT UNSIGNED NOT NULL,
    quantity     INT UNSIGNED NOT NULL DEFAULT 1,
    total_price  DECIMAL(10,2) NOT NULL,
    status       ENUM('pending','shipped','delivered','cancelled') DEFAULT 'pending',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_order_customer FOREIGN KEY (customer_id)
        REFERENCES customers(id) ON DELETE CASCADE,

    CONSTRAINT fk_order_product FOREIGN KEY (product_id)
        REFERENCES products(id) ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ============================================================================
-- SEED DATA — taake har query ka real output dekh sako
-- ============================================================================

INSERT INTO departments (name, location) VALUES
('Engineering', 'Lahore'),
('Sales', 'Karachi'),
('HR', 'Islamabad'),
('Marketing', 'Lahore');

-- managers pehle (manager_id NULL), phir unke subordinates
INSERT INTO employees (name, email, department_id, manager_id, salary, hire_date, status) VALUES
('Ahmed Raza',   'ahmed@company.com',   1, NULL, 250000, '2018-01-10', 'active'),   -- id 1: Eng Manager
('Bilal Khan',   'bilal@company.com',   2, NULL, 220000, '2018-03-15', 'active'),   -- id 2: Sales Manager
('Sara Ali',     'sara@company.com',    1, 1,    180000, '2019-05-20', 'active'),
('Hina Sheikh',  'hina@company.com',    1, 1,    175000, '2020-02-11', 'active'),
('Usman Tariq',  'usman@company.com',   1, 1,    175000, '2020-07-01', 'active'),   -- duplicate salary (tricky queries ke liye)
('Zara Malik',   'zara@company.com',    2, 2,    140000, '2019-09-09', 'active'),
('Faisal Iqbal', 'faisal@company.com',  2, 2,    135000, '2021-01-15', 'inactive'),
('Maria Yousaf', 'maria@company.com',   3, NULL, 160000, '2017-11-30', 'active'),
('Omar Sheikh',  'omar@company.com',    4, NULL, 150000, '2022-04-18', 'active'),
('Ayesha Noor',  'ayesha@company.com',  NULL, NULL, 90000, '2023-06-01', 'active'); -- department_id NULL (outer join ke liye)

INSERT INTO customers (name, email, city) VALUES
('Ali Hamza',   'ali.hamza@gmail.com', 'Lahore'),
('Sana Tariq',  'sana.tariq@gmail.com', 'Karachi'),
('Bilal Ahmed', 'bilal.ahmed@gmail.com', 'Lahore'),
('Noman Sheikh','noman.sheikh@gmail.com', 'Islamabad'); -- isay koi order nahi hoga (LEFT JOIN ke liye)

INSERT INTO products (name, category, price, stock) VALUES
('Laptop',      'Electronics', 120000, 25),
('Mouse',       'Electronics', 1500,   200),
('Office Chair','Furniture',   18000,  40),
('Notebook',    'Stationery',  150,    1000),
('Monitor',     'Electronics', 35000,  60);

INSERT INTO orders (customer_id, product_id, quantity, total_price, status, created_at) VALUES
(1, 1, 1, 120000, 'delivered', '2024-01-05'),
(1, 2, 2, 3000,   'delivered', '2024-01-05'),
(2, 3, 1, 18000,  'shipped',   '2024-02-10'),
(2, 5, 1, 35000,  'pending',   '2024-03-01'),
(3, 4, 5, 750,    'delivered', '2024-03-15'),
(1, 5, 1, 35000,  'cancelled', '2024-04-02');

-- Quick sanity check
SELECT 'departments' AS tbl, COUNT(*) FROM departments
UNION ALL SELECT 'employees', COUNT(*) FROM employees
UNION ALL SELECT 'customers', COUNT(*) FROM customers
UNION ALL SELECT 'products', COUNT(*) FROM products
UNION ALL SELECT 'orders', COUNT(*) FROM orders;
