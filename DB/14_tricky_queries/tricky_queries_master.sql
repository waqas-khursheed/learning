-- ============================================================================
--  TRICKY QUERIES — Live Coding Round ka Core
--  Ye sab CLASSIC interview questions hain — har ek ko bila dekhe likhne ki
--  practice karo. Multiple approaches diye hain jahan relevant hai.
-- ============================================================================
USE company_db;

-- ----------------------------------------------------------------------------
-- 1) Nth Highest Salary (SABSE ZYADA POOCHA JANE WALA SAWAAL)
-- ----------------------------------------------------------------------------

-- Approach A: DENSE_RANK (MODERN, BEST — ties ko sahi handle karta hai)
SELECT name, salary FROM (
    SELECT name, salary, DENSE_RANK() OVER (ORDER BY salary DESC) AS rnk
    FROM employees
) ranked
WHERE rnk = 2;   -- 2nd highest

-- Approach B: LIMIT + OFFSET (simple but ties handle nahi karta — duplicate
-- salary ho to galat answer de sakta hai)
SELECT DISTINCT salary FROM employees ORDER BY salary DESC LIMIT 1 OFFSET 1;
-- N-th highest: LIMIT 1 OFFSET (N-1)

-- Approach C: Correlated Subquery (OLD SCHOOL, MySQL 5.x compatible — index lagi ho to ok)
SELECT MAX(salary) AS second_highest
FROM employees
WHERE salary < (SELECT MAX(salary) FROM employees);

-- Approach D: Without window functions, generic Nth (N=3 example)
SELECT DISTINCT salary FROM employees e1
WHERE 2 = (SELECT COUNT(DISTINCT salary) FROM employees e2 WHERE e2.salary >= e1.salary)
ORDER BY salary;   -- 2nd highest (count of distinct salaries >= current = N)

-- ----------------------------------------------------------------------------
-- 2) Department-wise Nth Highest Salary
-- ----------------------------------------------------------------------------
SELECT name, department_id, salary FROM (
    SELECT name, department_id, salary,
           DENSE_RANK() OVER (PARTITION BY department_id ORDER BY salary DESC) AS rnk
    FROM employees
) ranked
WHERE rnk = 1;   -- har department ka HIGHEST paid employee

-- ----------------------------------------------------------------------------
-- 3) Duplicate Rows DHOONDNA
-- ----------------------------------------------------------------------------
-- "Konsi salaries duplicate hain" (1 se zyada employee ki same salary)
SELECT salary, COUNT(*) AS total
FROM employees
GROUP BY salary
HAVING COUNT(*) > 1;

-- Full duplicate ROWS dhoondna (saare columns match):
SELECT name, email, COUNT(*)
FROM employees
GROUP BY name, email
HAVING COUNT(*) > 1;

-- ----------------------------------------------------------------------------
-- 4) Duplicate Rows DELETE karna (sirf 1 copy rakhni hai)
-- ----------------------------------------------------------------------------
-- Approach: ROW_NUMBER se rank do, phir rank > 1 wali rows delete karo
-- (MySQL me UPDATE/DELETE ke sath subquery FROM-clause restriction hai,
--  isliye derived table ko ek extra wrap me dena padta hai)
DELETE FROM employees
WHERE id IN (
    SELECT id FROM (
        SELECT id,
               ROW_NUMBER() OVER (PARTITION BY email ORDER BY id) AS rn
        FROM employees
    ) t
    WHERE t.rn > 1
);
-- (Demo data me duplicates nahi hain, lekin pattern yehi hai)

-- ----------------------------------------------------------------------------
-- 5) Employees jo apne MANAGER se zyada salary lete hain (SELF JOIN)
-- ----------------------------------------------------------------------------
SELECT emp.name AS employee, emp.salary AS emp_salary,
       mgr.name AS manager, mgr.salary AS mgr_salary
FROM employees emp
JOIN employees mgr ON emp.manager_id = mgr.id
WHERE emp.salary > mgr.salary;

-- ----------------------------------------------------------------------------
-- 6) Consecutive Numbers / GAPS & ISLANDS Problem (CLASSIC, tricky)
-- ----------------------------------------------------------------------------
-- "Order IDs me konse GAPS hain (missing numbers)?"
WITH numbered AS (
    SELECT id, LEAD(id) OVER (ORDER BY id) AS next_id
    FROM orders
)
SELECT id AS gap_starts_after, next_id AS gap_ends_before
FROM numbered
WHERE next_id - id > 1;
-- Agar id 3, 4, 6, 7 hain, to ye dikhayega: gap between 4 and 6

-- "ISLANDS" — consecutive groups dhoondna (e.g. consecutive dates jab order hue):
WITH grp AS (
    SELECT id, created_at,
           ROW_NUMBER() OVER (ORDER BY created_at) -
           DATEDIFF(created_at, '2024-01-01') AS island_group
    FROM orders
)
SELECT island_group, MIN(created_at) AS island_start, MAX(created_at) AS island_end, COUNT(*) AS days
FROM grp
GROUP BY island_group;
-- Trick: (row_number - date_diff) consecutive dates ke liye SAME value
-- deta hai — yehi GAPS & ISLANDS ka standard pattern hai

-- ----------------------------------------------------------------------------
-- 7) Pivot Table (Rows ko Columns me convert karna — MySQL me PIVOT keyword nahi hai)
-- ----------------------------------------------------------------------------
SELECT
    d.name AS department,
    SUM(CASE WHEN e.status = 'active' THEN 1 ELSE 0 END) AS active,
    SUM(CASE WHEN e.status = 'inactive' THEN 1 ELSE 0 END) AS inactive,
    SUM(CASE WHEN e.status = 'terminated' THEN 1 ELSE 0 END) AS terminated
FROM departments d
LEFT JOIN employees e ON e.department_id = d.id
GROUP BY d.id, d.name;

-- ----------------------------------------------------------------------------
-- 8) Running Total / Cumulative Sum
-- ----------------------------------------------------------------------------
SELECT id, created_at, total_price,
       SUM(total_price) OVER (ORDER BY created_at, id) AS running_total
FROM orders;

-- ----------------------------------------------------------------------------
-- 9) Find employees WITHOUT a manager assigned, aur unke SAARE subordinates count
-- ----------------------------------------------------------------------------
SELECT mgr.name AS manager, COUNT(emp.id) AS direct_reports
FROM employees mgr
LEFT JOIN employees emp ON emp.manager_id = mgr.id
WHERE mgr.manager_id IS NULL
GROUP BY mgr.id, mgr.name;

-- ----------------------------------------------------------------------------
-- 10) Swap odd/even row values (CLASSIC PUZZLE — using window function)
-- ----------------------------------------------------------------------------
-- "Department list me consecutive pairs ke naam swap karo (1<->2, 3<->4)"
SELECT
    id,
    CASE
        WHEN id % 2 = 1 AND id < (SELECT MAX(id) FROM departments) THEN
            (SELECT name FROM departments d2 WHERE d2.id = d1.id + 1)
        WHEN id % 2 = 0 THEN
            (SELECT name FROM departments d2 WHERE d2.id = d1.id - 1)
        ELSE name
    END AS swapped_name
FROM departments d1
ORDER BY id;

-- ----------------------------------------------------------------------------
-- 11) Customers jinhone EVERY product category se kam se kam 1 order kiya ho
--     (RELATIONAL DIVISION pattern — bohat tricky, interview "hard" level)
-- ----------------------------------------------------------------------------
SELECT c.id, c.name
FROM customers c
WHERE NOT EXISTS (
    -- har category check karo
    SELECT p.category FROM products p
    WHERE NOT EXISTS (
        -- ye category is customer ne order ki hai?
        SELECT 1 FROM orders o
        JOIN products p2 ON o.product_id = p2.id
        WHERE o.customer_id = c.id AND p2.category = p.category
    )
);
-- "Double NOT EXISTS" = Relational Division technique:
-- "Aisa koi category NA ho jo is customer ne order na ki ho"

-- ----------------------------------------------------------------------------
-- 12) Median Salary (MySQL me MEDIAN() function nahi hai — manually calculate)
-- ----------------------------------------------------------------------------
WITH ordered AS (
    SELECT salary, ROW_NUMBER() OVER (ORDER BY salary) AS rn, COUNT(*) OVER () AS total
    FROM employees
)
SELECT AVG(salary) AS median_salary
FROM ordered
WHERE rn IN ( FLOOR((total + 1) / 2), CEIL((total + 1) / 2) );
-- Odd count: dono FLOOR/CEIL same row -> us row ki value
-- Even count: dono middle rows -> unka average

-- ----------------------------------------------------------------------------
-- 13) Top 3 products per category by price (per-group top-N — already seen
--     in window_functions.sql, yahan revision)
-- ----------------------------------------------------------------------------
SELECT * FROM (
    SELECT name, category, price,
           ROW_NUMBER() OVER (PARTITION BY category ORDER BY price DESC) AS rn
    FROM products
) t WHERE rn <= 3;

-- ----------------------------------------------------------------------------
-- 14) Find the SECOND most recent order per customer
-- ----------------------------------------------------------------------------
SELECT * FROM (
    SELECT o.*, ROW_NUMBER() OVER (PARTITION BY customer_id ORDER BY created_at DESC) AS rn
    FROM orders o
) t WHERE rn = 2;

-- ----------------------------------------------------------------------------
-- 15) Convert comma-separated string ko ROWS me (string splitting — MySQL
--     me native SPLIT function nahi hai, JSON_TABLE se 8.0+ me karte hain)
-- ----------------------------------------------------------------------------
SELECT jt.value
FROM JSON_TABLE(
    JSON_ARRAY('Lahore', 'Karachi', 'Islamabad'),
    '$[*]' COLUMNS (value VARCHAR(50) PATH '$')
) AS jt;
-- Real use: agar ek column me 'tag1,tag2,tag3' store hai (jo anyway 1NF
-- violation hai — schema fix karna better solution hai, ye sirf workaround hai)

-- ============================================================================
-- PRACTICE CHECKLIST (in sab ko BILA DEKHE likhne ki koshish karo)
-- ============================================================================
-- [ ] Nth highest salary (with ties handled correctly)
-- [ ] Department-wise top earner
-- [ ] Find duplicates + delete duplicates keeping one
-- [ ] Employee vs Manager self-join comparison
-- [ ] Running total with window function
-- [ ] Gaps & Islands pattern
-- [ ] Pivot with CASE WHEN
-- [ ] Median calculation
-- [ ] Relational division ("customers who bought ALL categories")
-- [ ] Top-N per group
