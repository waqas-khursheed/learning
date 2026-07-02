-- ============================================================================
--  JOINS — Roz ka kaam, interview me 100% poocha jata hai
-- ============================================================================
USE company_db;

-- Reference: schema diagram
--   departments (id) ---< employees (department_id, manager_id self-ref)
--   customers (id) ---< orders (customer_id) >--- products (id)

-- ----------------------------------------------------------------------------
-- 1) INNER JOIN — Sirf wo rows jin ka match DONO tables me ho
-- ----------------------------------------------------------------------------
SELECT e.name AS employee, d.name AS department
FROM employees e
INNER JOIN departments d ON e.department_id = d.id;
-- Ayesha Noor is result me NAHI aayegi (uska department_id NULL hai -> match nahi)

-- ----------------------------------------------------------------------------
-- 2) LEFT JOIN (LEFT OUTER JOIN) — LEFT table ki SAARI rows + matching right rows
--    (no match -> right side columns NULL)
-- ----------------------------------------------------------------------------
SELECT e.name AS employee, d.name AS department
FROM employees e
LEFT JOIN departments d ON e.department_id = d.id;
-- Ab Ayesha Noor bhi aayegi, department = NULL

-- Real use-case: "Konse customers ne KOI order nahi kiya?" (LEFT JOIN + IS NULL)
SELECT c.name
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
WHERE o.id IS NULL;
-- Result: Noman Sheikh (seed data me uska koi order nahi)

-- ----------------------------------------------------------------------------
-- 3) RIGHT JOIN (RIGHT OUTER JOIN) — RIGHT table ki saari rows + matching left
--    (MySQL me kam use hota hai — LEFT JOIN se table order swap karke same result mil jata hai)
-- ----------------------------------------------------------------------------
SELECT e.name AS employee, d.name AS department
FROM departments d
RIGHT JOIN employees e ON e.department_id = d.id;
-- Yeh upar wale LEFT JOIN ke EXACT same result deta hai (tables ka order swap kiya)

-- ----------------------------------------------------------------------------
-- 4) FULL OUTER JOIN — MySQL me directly support NAHI hai!
--    (PostgreSQL/SQL Server me FULL OUTER JOIN keyword hota hai)
--    MySQL me LEFT JOIN UNION RIGHT JOIN se simulate karte hain
-- ----------------------------------------------------------------------------
SELECT e.name AS employee, d.name AS department
FROM employees e
LEFT JOIN departments d ON e.department_id = d.id
UNION
SELECT e.name AS employee, d.name AS department
FROM employees e
RIGHT JOIN departments d ON e.department_id = d.id;
-- UNION (not UNION ALL) duplicate matching rows ko apne aap remove kar deta hai

-- ----------------------------------------------------------------------------
-- 5) SELF JOIN — Table apne aap se join, jab relation isi table ke andar ho
-- ----------------------------------------------------------------------------
-- Classic use-case: Employee + uska Manager (dono employees table me hain)
SELECT
    emp.name AS employee_name,
    mgr.name AS manager_name
FROM employees emp
LEFT JOIN employees mgr ON emp.manager_id = mgr.id;
-- LEFT JOIN isliye kyunki Ahmed/Bilal/Maria/Omar/Ayesha ke manager_id NULL hain

-- ----------------------------------------------------------------------------
-- 6) CROSS JOIN — Cartesian Product (har row x har row, NO condition)
-- ----------------------------------------------------------------------------
-- Use-case: Saare possible (product x category-discount-tier) combinations banana
SELECT p.name, d.name AS department_name
FROM products p
CROSS JOIN departments d;
-- Result rows = products count * departments count (5 * 4 = 20)
-- ⚠️ GOTCHA: Agar bina sochay WHERE clause bhool jao ya CROSS JOIN galti se
-- ho jaye (comma join: FROM a, b without WHERE), to accidental cartesian
-- product ban jata hai — bohat common production bug hai!

-- ----------------------------------------------------------------------------
-- 7) Multiple JOINS together (real-world query)
-- ----------------------------------------------------------------------------
SELECT
    c.name AS customer,
    p.name AS product,
    o.quantity,
    o.total_price,
    o.status
FROM orders o
INNER JOIN customers c ON o.customer_id = c.id
INNER JOIN products p ON o.product_id = p.id
WHERE o.status != 'cancelled'
ORDER BY o.created_at DESC;

-- ----------------------------------------------------------------------------
-- 8) JOIN with aggregate (employees per department + their total salary)
-- ----------------------------------------------------------------------------
SELECT
    d.name AS department,
    COUNT(e.id) AS total_employees,
    SUM(e.salary) AS total_salary,
    AVG(e.salary) AS avg_salary
FROM departments d
LEFT JOIN employees e ON e.department_id = d.id
GROUP BY d.id, d.name
ORDER BY total_salary DESC;

-- ----------------------------------------------------------------------------
-- 9) USING vs ON
-- ----------------------------------------------------------------------------
-- Agar dono tables me column ka NAAM SAME ho to USING use kar sakte ho:
-- (department_id name match nahi karta dono tables me, isliye demo ke liye)
SELECT o.id, c.name
FROM orders o
JOIN customers c ON o.customer_id = c.id;
-- agar customers.id ko hum orders me bhi "id" rakhte (jo bad practice hai) to:
-- JOIN customers c USING (id)  -- sirf jab column NAME same ho

-- ----------------------------------------------------------------------------
-- 10) JOIN execution internals (kaise kaam karta hai engine ke andar)
-- ----------------------------------------------------------------------------
-- MySQL (InnoDB) JOINS ko execute karne ke liye in algorithms me se ek use karta hai:
--
-- a) Nested Loop Join (NLJ) — DEFAULT algorithm
--    Outer table ki har row ke liye, inner table me MATCHING row dhoondhta hai.
--    Agar inner table ke join column pe INDEX hai -> bohat fast (Index Nested Loop)
--    Agar INDEX nahi hai -> har outer row ke liye poori inner table scan (SLOW, O(n*m))
--
-- b) Block Nested Loop Join (BNL) — jab index na ho, MySQL ek "join buffer"
--    (RAM) me outer table ke rows load karta hai, batch me compare karta hai
--    (purane MySQL me). MySQL 8.0.18+ me iski jagah "Hash Join" use hota hai.
--
-- c) Hash Join (MySQL 8.0.18+) — Equi-joins (=) ke liye, jab index na ho,
--    optimizer ek table (chhoti) ka in-memory HASH TABLE banata hai, phir
--    doosri table scan karke hash lookup karta hai. BNL se bohat fast hai.
--
-- ⚠️ MOST IMPORTANT TAKEAWAY: JOIN column par INDEX hona chahiye (especially
-- foreign key columns) — warna bade tables pe join EXTREMELY slow ho jata hai.
-- Verify karne ke liye: EXPLAIN ANALYZE <query> (detail: 08_query_optimization/)

EXPLAIN ANALYZE
SELECT e.name, d.name
FROM employees e
JOIN departments d ON e.department_id = d.id;

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: INNER JOIN aur WHERE clause join (comma join, old style) me kya farq hai?
-- A: Result same ho sakta hai lekin INNER JOIN explicit aur readable hai,
--    aur galti se CROSS JOIN ban'ne ka risk kam hota hai. Modern style
--    hamesha explicit JOIN...ON use karta hai.
--
-- Q: LEFT JOIN ke baad WHERE me right table ka column use karu to kya hota hai?
-- A: GOTCHA — agar WHERE me right table ka non-NULL filter lagao, to LEFT
--    JOIN effectively INNER JOIN ban jata hai! Filter ON clause me lagao
--    agar LEFT JOIN ka behavior chahiye:
--    -- GALAT (LEFT JOIN ka fayda khatam):
--    SELECT * FROM employees e LEFT JOIN departments d ON e.department_id=d.id
--    WHERE d.location = 'Lahore';
--    -- SAHI:
--    SELECT * FROM employees e LEFT JOIN departments d
--    ON e.department_id = d.id AND d.location = 'Lahore';
--
-- Q: 3 tables join karne ka best practice kya hai?
-- A: Sabse SELECTIVE (chhoti/filtered) table ko pehle join karo, aur
--    HAMESHA join columns pe index confirm karo. Optimizer khud bhi
--    reorder karta hai (cost-based) lekin readability ke liye logical order rakho.
