-- ============================================================================
--  SUBQUERIES, CTEs & UNIONS
-- ============================================================================
USE company_db;

-- ----------------------------------------------------------------------------
-- 1) SUBQUERY TYPES
-- ----------------------------------------------------------------------------

-- a) Scalar Subquery (single value return karti hai)
SELECT name, salary,
       (SELECT AVG(salary) FROM employees) AS company_avg_salary
FROM employees;

-- b) Column Subquery with IN
SELECT name FROM employees
WHERE department_id IN (SELECT id FROM departments WHERE location = 'Lahore');

-- c) Correlated Subquery (inner query OUTER query ke column ko reference karti hai
--    -> har outer row ke liye baar baar execute hoti hai, isliye SLOW ho sakti hai)
SELECT e.name, e.salary, e.department_id
FROM employees e
WHERE e.salary > (
    SELECT AVG(e2.salary) FROM employees e2 WHERE e2.department_id = e.department_id
);
-- "Apne department ke average se zyada salary lene wale employees"

-- d) EXISTS / NOT EXISTS (correlated, BOOLEAN check — index ho to bohat fast)
SELECT c.name
FROM customers c
WHERE EXISTS (SELECT 1 FROM orders o WHERE o.customer_id = c.id AND o.status = 'delivered');

SELECT c.name
FROM customers c
WHERE NOT EXISTS (SELECT 1 FROM orders o WHERE o.customer_id = c.id);
-- "Konse customers ne kabhi order nahi kiya" — same result jo LEFT JOIN+IS NULL se mila tha
-- (03_joins/joins.sql me) — DONO approach ka result same, performance EXPLAIN se compare karo

-- e) Subquery in FROM (Derived Table) — must have alias
SELECT dept_stats.department, dept_stats.avg_salary
FROM (
    SELECT d.name AS department, AVG(e.salary) AS avg_salary
    FROM employees e
    JOIN departments d ON e.department_id = d.id
    GROUP BY d.name
) AS dept_stats
WHERE dept_stats.avg_salary > 150000;

-- f) ANY / ALL
SELECT name, salary FROM employees
WHERE salary > ANY (SELECT salary FROM employees WHERE department_id = 2);
-- "kam se kam ek Sales employee se zyada salary" (max nahi, "any" = at least one se zyada)

SELECT name, salary FROM employees
WHERE salary > ALL (SELECT salary FROM employees WHERE department_id = 2);
-- "SAARE Sales employees se zyada salary" (= max(Sales salary) se zyada)

-- ----------------------------------------------------------------------------
-- 2) CTE — Common Table Expression (WITH clause, MySQL 8.0+)
-- ----------------------------------------------------------------------------
-- Subquery ka readable/reusable version. Complex queries ko steps me todta hai.

WITH dept_avg AS (
    SELECT department_id, AVG(salary) AS avg_salary
    FROM employees
    GROUP BY department_id
)
SELECT e.name, e.salary, d.avg_salary
FROM employees e
JOIN dept_avg d ON e.department_id = d.department_id
WHERE e.salary > d.avg_salary;

-- Multiple CTEs (ek dusre ko bhi reference kar sakte hain):
WITH
high_earners AS (
    SELECT id, name, department_id FROM employees WHERE salary > 150000
),
dept_names AS (
    SELECT id, name FROM departments
)
SELECT h.name AS employee, dn.name AS department
FROM high_earners h
JOIN dept_names dn ON h.department_id = dn.id;

-- ----------------------------------------------------------------------------
-- 3) RECURSIVE CTE (org chart / hierarchy traverse karne ke liye — interview favourite)
-- ----------------------------------------------------------------------------
-- Pura management chain dikhana: kisi employee se le kar top tak ka manager chain
WITH RECURSIVE manager_chain AS (
    -- Anchor: starting employee
    SELECT id, name, manager_id, 1 AS level
    FROM employees
    WHERE name = 'Sara Ali'

    UNION ALL

    -- Recursive part: parent (manager) dhoondo
    SELECT e.id, e.name, e.manager_id, mc.level + 1
    FROM employees e
    INNER JOIN manager_chain mc ON e.id = mc.manager_id
)
SELECT * FROM manager_chain;

-- Pura org tree top-down dikhana (sabse senior se sab tak):
WITH RECURSIVE org_tree AS (
    SELECT id, name, manager_id, 0 AS depth, CAST(name AS CHAR(500)) AS path
    FROM employees
    WHERE manager_id IS NULL

    UNION ALL

    SELECT e.id, e.name, e.manager_id, ot.depth + 1, CONCAT(ot.path, ' -> ', e.name)
    FROM employees e
    INNER JOIN org_tree ot ON e.manager_id = ot.id
)
SELECT * FROM org_tree ORDER BY depth, name;

-- ----------------------------------------------------------------------------
-- 4) UNION vs UNION ALL (CLASSIC INTERVIEW Q)
-- ----------------------------------------------------------------------------
-- Rules: dono SELECTs me SAME number of columns + compatible data types

-- UNION: duplicates ko REMOVE karta hai (internally DISTINCT + sort/hash) -> SLOWER
SELECT name, 'employee' AS type FROM employees
UNION
SELECT name, 'customer' AS type FROM customers;

-- UNION ALL: duplicates allow karta hai -> FASTER (dedupe ka extra cost nahi)
SELECT name, 'employee' AS type FROM employees
UNION ALL
SELECT name, 'customer' AS type FROM customers;

-- ⚠️ PERFORMANCE RULE: Agar pata hai duplicates nahi aayenge (ya unko allow
-- karna fine hai), HAMESHA UNION ALL use karo — UNION ka duplicate-removal
-- step (sort/hash distinct) bade result sets pe MEHENGA hota hai.

-- ----------------------------------------------------------------------------
-- 5) Subquery vs JOIN vs CTE — kab kya use karein
-- ----------------------------------------------------------------------------
-- | Approach    | Kab use karo                                                      |
-- |--------------|---------------------------------------------------------------------|
-- | JOIN         | Jab dono tables ka data ek row me CHAHIYE (columns dono se)        |
-- | EXISTS       | Sirf "haan/nahi" check karna ho (existence), actual data nahi chahiye |
-- | IN/Subquery  | Chhoti static list ke against filter karna ho                      |
-- | CTE          | Query readable banana ho, ya RECURSIVE hierarchy traverse karni ho   |
-- | Correlated   | Per-row comparison chahiye (e.g. "apne group ke avg se zyada")      |
--   Subquery
--
-- Performance tip: MySQL optimizer (8.0.16+) correlated subqueries aur
-- semi-joins ko automatically optimize karta hai, lekin manually EXPLAIN
-- karke verify karna chahiye — kabhi kabhi JOIN rewrite subquery se fast hota hai.
