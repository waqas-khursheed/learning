-- ============================================================================
--  AGGREGATION & GROUPING — Dashboards/Reports ka backbone
-- ============================================================================
USE company_db;

-- ----------------------------------------------------------------------------
-- 1) Aggregate Functions
-- ----------------------------------------------------------------------------
SELECT
    COUNT(*)              AS total_employees,     -- saari rows (NULL bhi count)
    COUNT(department_id)  AS with_department,      -- NULL skip karta hai
    COUNT(DISTINCT department_id) AS distinct_departments,
    SUM(salary)            AS total_payroll,
    AVG(salary)            AS avg_salary,
    MIN(salary)            AS lowest_salary,
    MAX(salary)            AS highest_salary,
    STDDEV(salary)         AS salary_stddev
FROM employees;

-- ⚠️ GOTCHA: COUNT(*) vs COUNT(column) vs COUNT(1)
-- COUNT(*)      -> saari rows count, NULLs ignore nahi karta (row exist = count hoga)
-- COUNT(column) -> sirf non-NULL values count
-- COUNT(1)      -> COUNT(*) jaisa hi performance (MySQL optimizer dono ko same treat karta hai)

-- ----------------------------------------------------------------------------
-- 2) GROUP BY
-- ----------------------------------------------------------------------------
SELECT department_id, COUNT(*) AS total, AVG(salary) AS avg_salary
FROM employees
GROUP BY department_id;

-- GROUP BY multiple columns:
SELECT department_id, status, COUNT(*) AS total
FROM employees
GROUP BY department_id, status;

-- ⚠️ ONLY_FULL_GROUP_BY (MySQL 5.7+ default mode):
-- SELECT me jo bhi column non-aggregate hai, wo GROUP BY me hona ZARURI hai
-- (warna error: "not functionally dependent"). Ye ANSI SQL standard hai.
SHOW VARIABLES LIKE 'sql_mode';

-- ----------------------------------------------------------------------------
-- 3) WHERE vs HAVING (CLASSIC INTERVIEW Q)
-- ----------------------------------------------------------------------------
-- WHERE  -> Grouping se PEHLE individual rows filter karta hai (aggregate fn use NAHI kar sakta)
-- HAVING -> Grouping ke BAAD groups filter karta hai (aggregate fn use kar sakta hai)

SELECT department_id, AVG(salary) AS avg_salary, COUNT(*) AS total
FROM employees
WHERE status = 'active'              -- pehle: sirf active employees lo
GROUP BY department_id
HAVING AVG(salary) > 150000 AND COUNT(*) >= 2;   -- phir: jin groups ka avg > 150000 ho

-- Logical execution order (yehi interview me sabse zyada poocha jata hai):
-- FROM -> JOIN -> WHERE -> GROUP BY -> HAVING -> SELECT -> DISTINCT -> ORDER BY -> LIMIT

-- ----------------------------------------------------------------------------
-- 4) ROLLUP — subtotal aur grand total ek hi query me
-- ----------------------------------------------------------------------------
SELECT department_id, status, COUNT(*) AS total
FROM employees
GROUP BY department_id, status WITH ROLLUP;
-- Extra rows aayengi: har department ka subtotal (status=NULL) + grand total (dono NULL)

-- ----------------------------------------------------------------------------
-- 5) GROUP_CONCAT — group ki values ko ek string me jor dena
-- ----------------------------------------------------------------------------
SELECT d.name AS department, GROUP_CONCAT(e.name ORDER BY e.salary DESC SEPARATOR ', ') AS employees_list
FROM departments d
JOIN employees e ON e.department_id = d.id
GROUP BY d.id, d.name;
-- Result: "Engineering" -> "Ahmed Raza, Sara Ali, Usman Tariq, Hina Sheikh"

-- ----------------------------------------------------------------------------
-- 6) Conditional Aggregation (CASE WHEN + aggregate -> Excel jaisa PIVOT)
-- ----------------------------------------------------------------------------
SELECT
    department_id,
    COUNT(CASE WHEN status = 'active' THEN 1 END)   AS active_count,
    COUNT(CASE WHEN status = 'inactive' THEN 1 END) AS inactive_count,
    SUM(CASE WHEN salary > 150000 THEN salary ELSE 0 END) AS high_earner_payroll
FROM employees
GROUP BY department_id;

-- Order status-wise revenue (pivot table jaisa report):
SELECT
    SUM(CASE WHEN status = 'delivered' THEN total_price ELSE 0 END) AS delivered_revenue,
    SUM(CASE WHEN status = 'pending'   THEN total_price ELSE 0 END) AS pending_revenue,
    SUM(CASE WHEN status = 'cancelled' THEN total_price ELSE 0 END) AS cancelled_revenue
FROM orders;

-- ----------------------------------------------------------------------------
-- 7) HAVING with JOIN + multiple aggregates (real report query)
-- ----------------------------------------------------------------------------
-- "Wo customers jinhone 30000 se zyada ka total order kiya"
SELECT c.name, SUM(o.total_price) AS total_spent, COUNT(o.id) AS order_count
FROM customers c
JOIN orders o ON o.customer_id = c.id
WHERE o.status != 'cancelled'
GROUP BY c.id, c.name
HAVING SUM(o.total_price) > 30000
ORDER BY total_spent DESC;

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: WHERE me aggregate function (AVG, SUM) kyun use nahi kar sakte?
-- A: Kyunki WHERE GROUP BY se PEHLE execute hoti hai (logical order), us waqt
--    groups bane hi nahi hote, isliye aggregate value available nahi hoti.
--    HAVING groups ban'ne ke baad chalti hai.
--
-- Q: COUNT(*) slow hota hai bade table pe?
-- A: InnoDB me COUNT(*) WITHOUT WHERE poori table scan karta hai (MyISAM ki
--    tarah stored count metadata nahi rakhta InnoDB MVCC ki wajah se).
--    Approximate count chahiye ho to: SELECT table_rows FROM
--    information_schema.tables WHERE table_name='employees'; (estimate hai, exact nahi)
--
-- Q: GROUP BY aur DISTINCT me farq?
-- A: DISTINCT sirf duplicate ROWS remove karta hai. GROUP BY rows ko groups
--    me todta hai taake unpe AGGREGATE FUNCTIONS apply ho sakein. DISTINCT
--    ko GROUP BY (bina aggregate ke) ka special case bhi keh sakte ho.
