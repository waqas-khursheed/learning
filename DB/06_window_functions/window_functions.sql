-- ============================================================================
--  WINDOW FUNCTIONS (MySQL 8.0+) — Senior-level differentiator
--  GROUP BY rows ko COLLAPSE kar deta hai, lekin WINDOW FUNCTION rows ko
--  collapse NAHI karta — har row ke sath ek "calculated column" add hoti hai.
-- ============================================================================
USE company_db;

-- ----------------------------------------------------------------------------
-- 1) Syntax Pattern
-- ----------------------------------------------------------------------------
-- <function>() OVER (
--     PARTITION BY <column>   -- (optional) data ko groups me todta hai (GROUP BY jaisa, lekin collapse nahi karta)
--     ORDER BY <column>       -- (optional) partition ke andar order
--     <frame clause>           -- (optional) ROWS/RANGE BETWEEN ...
-- )

-- ----------------------------------------------------------------------------
-- 2) ROW_NUMBER() — Har row ko sequential number deta hai (kabhi tie nahi)
-- ----------------------------------------------------------------------------
SELECT
    name, department_id, salary,
    ROW_NUMBER() OVER (PARTITION BY department_id ORDER BY salary DESC) AS row_num
FROM employees;
-- Har department ke andar salary ke hisab se 1, 2, 3... number milta hai

-- ----------------------------------------------------------------------------
-- 3) RANK() vs DENSE_RANK() — Tie hone par farq (CLASSIC INTERVIEW Q)
-- ----------------------------------------------------------------------------
SELECT
    name, department_id, salary,
    RANK()       OVER (PARTITION BY department_id ORDER BY salary DESC) AS rnk,
    DENSE_RANK() OVER (PARTITION BY department_id ORDER BY salary DESC) AS dense_rnk
FROM employees
WHERE department_id = 1;
-- Engineering dept me Hina aur Usman dono ki salary 175000 hai (TIE):
-- RANK:        1, 2, 2, 4   (tie ke baad number SKIP hota hai)
-- DENSE_RANK:  1, 2, 2, 3   (tie ke baad number SKIP nahi hota)

-- ----------------------------------------------------------------------------
-- 4) NTILE(n) — Data ko N equal buckets me todna (e.g. quartiles)
-- ----------------------------------------------------------------------------
SELECT name, salary, NTILE(4) OVER (ORDER BY salary DESC) AS salary_quartile
FROM employees;

-- ----------------------------------------------------------------------------
-- 5) LAG() / LEAD() — Pichli/Agli row ki value (time-series jaisa)
-- ----------------------------------------------------------------------------
SELECT
    id, created_at, total_price,
    LAG(total_price)  OVER (ORDER BY created_at) AS previous_order_amount,
    LEAD(total_price) OVER (ORDER BY created_at) AS next_order_amount,
    total_price - LAG(total_price) OVER (ORDER BY created_at) AS diff_from_previous
FROM orders;
-- Use-case: "Month-over-month growth", "previous order se kitna farq hai"

-- ----------------------------------------------------------------------------
-- 6) FIRST_VALUE() / LAST_VALUE()
-- ----------------------------------------------------------------------------
SELECT
    name, department_id, salary,
    FIRST_VALUE(name) OVER (PARTITION BY department_id ORDER BY salary DESC) AS top_earner_in_dept
FROM employees;
-- Har row pe uske department ka "highest paid employee ka naam" dikhega

-- ----------------------------------------------------------------------------
-- 7) Running Total / Moving Average (frame clause ke sath)
-- ----------------------------------------------------------------------------
SELECT
    id, created_at, total_price,
    SUM(total_price) OVER (ORDER BY created_at ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS running_total,
    AVG(total_price) OVER (ORDER BY created_at ROWS BETWEEN 2 PRECEDING AND CURRENT ROW) AS moving_avg_3
FROM orders
WHERE status != 'cancelled';

-- Frame clause explained:
-- ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW -> start se ab tak (running total)
-- ROWS BETWEEN 2 PRECEDING AND CURRENT ROW         -> current + pichli 2 rows (3-row moving avg)
-- ROWS BETWEEN CURRENT ROW AND UNBOUNDED FOLLOWING -> ab se end tak

-- ----------------------------------------------------------------------------
-- 8) Aggregate function as Window function (SUM/AVG/COUNT with OVER)
-- ----------------------------------------------------------------------------
SELECT
    name, department_id, salary,
    SUM(salary) OVER (PARTITION BY department_id) AS dept_total_salary,
    salary / SUM(salary) OVER (PARTITION BY department_id) * 100 AS pct_of_dept_payroll,
    AVG(salary) OVER (PARTITION BY department_id) AS dept_avg_salary,
    salary - AVG(salary) OVER (PARTITION BY department_id) AS diff_from_dept_avg
FROM employees;
-- Ye GROUP BY se BEHTAR hai yahan kyunki har EMPLOYEE row alag se chahiye,
-- saath me uske department ka aggregate bhi — GROUP BY ye ek query me nahi de sakta.

-- ----------------------------------------------------------------------------
-- 9) PRACTICAL: Nth Highest Salary per Department (window function ka real use)
-- ----------------------------------------------------------------------------
WITH ranked AS (
    SELECT name, department_id, salary,
           DENSE_RANK() OVER (PARTITION BY department_id ORDER BY salary DESC) AS rnk
    FROM employees
)
SELECT * FROM ranked WHERE rnk = 2;   -- har department ka 2nd highest paid employee

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: Window function aur GROUP BY me fundamental farq kya hai?
-- A: GROUP BY rows ko COLLAPSE/AGGREGATE karta hai (N rows -> kam rows).
--    Window function HAR row ko ALAG rakhta hai, sirf ek extra calculated
--    column add karta hai (N rows -> N rows, with extra context).
--
-- Q: RANK, DENSE_RANK, ROW_NUMBER me se kab kya use karoge?
-- A: ROW_NUMBER  -> jab unique sequential number chahiye ho (pagination,
--                   "sirf top 1 per group" lena ho — ties matter nahi karti)
--    RANK        -> jab ties allow karni hon lekin gaps bhi chahiye ho
--                   (sports leaderboard jaisa: 1,2,2,4)
--    DENSE_RANK  -> jab ties allow karni hon BUT gaps NAHI chahiye (1,2,2,3)
--
-- Q: "Each group ka top N rows lena" kaise karoge?
-- A: ROW_NUMBER() OVER (PARTITION BY group_col ORDER BY sort_col DESC) ko
--    CTE/derived table me wrap karke WHERE row_num <= N lagao (GROUP BY se
--    ye possible nahi hai directly).
--
-- Q: Window functions WHERE clause me directly use kyun nahi kar sakte?
-- A: Logical execution order me WHERE, window functions se PEHLE evaluate
--    hoti hai (window functions SELECT list/ORDER BY stage pe evaluate
--    hoti hain). Isliye filter karne ke liye CTE/subquery me wrap karke
--    bahar WHERE lagana padta hai (jaisa upar Nth salary example me kiya).
