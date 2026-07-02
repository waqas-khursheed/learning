-- ============================================================================
--  QUERY OPTIMIZATION — EXPLAIN, Optimizer, Slow Queries, N+1
-- ============================================================================
USE company_db;

-- ----------------------------------------------------------------------------
-- 1) EXPLAIN — Query execution plan dikhata hai (RUN nahi karta query ko)
-- ----------------------------------------------------------------------------
EXPLAIN SELECT * FROM employees WHERE department_id = 1;

-- EXPLAIN output ke important columns:
-- | Column         | Matlab                                                          |
-- |------------------|---------------------------------------------------------------------|
-- | id               | Query/subquery ka number (multiple ho sakte hain joins/subqueries me) |
-- | select_type      | SIMPLE, PRIMARY, SUBQUERY, DERIVED, UNION, etc.                   |
-- | table            | Konsi table access ho rahi hai                                    |
-- | type             | JOIN/ACCESS TYPE — SABSE IMPORTANT COLUMN (neeche detail)         |
-- | possible_keys    | Konsi indexes USE HO SAKTI THIN (candidates)                       |
-- | key              | ACTUALLY konsi index use hui (NULL = koi index use nahi hui!)      |
-- | key_len          | Index ka kitna part use hua (composite index debug karne ke liye) |
-- | rows             | ESTIMATE — kitni rows scan karni padengi (kam = behtar)            |
-- | filtered         | % rows jo WHERE condition ke baad bachengi                        |
-- | Extra            | "Using filesort", "Using temporary", "Using index" etc.            |

-- ----------------------------------------------------------------------------
-- 2) "type" column — Access Type (BEST se WORST tak, interview me ratta lagana zaruri)
-- ----------------------------------------------------------------------------
-- system/const  -> Best. Sirf 1 row match (e.g. WHERE id = 5, PK pe exact match)
-- eq_ref         -> JOIN me ek hi matching row (unique index pe join)
-- ref             -> Non-unique index pe equality match (e.g. WHERE department_id = 1)
-- range           -> Index range scan (BETWEEN, >, <, IN)
-- index           -> Pura INDEX scan hota hai (poori table se behtar but still slow)
-- ALL             -> WORST. FULL TABLE SCAN (koi index use nahi hui!)
--
-- GOAL: Apni queries me "ALL" se bachna hai. Kam se kam "range" ya behtar chahiye.

EXPLAIN SELECT * FROM employees WHERE id = 3;                  -- type: const (best)
EXPLAIN SELECT * FROM employees WHERE department_id = 1;       -- type: ref (index hai to)
EXPLAIN SELECT * FROM employees WHERE salary BETWEEN 100000 AND 200000;  -- type: range (if indexed)
EXPLAIN SELECT * FROM employees;                                -- type: ALL (full scan, normal hai bina WHERE ke)

-- ----------------------------------------------------------------------------
-- 3) EXPLAIN ANALYZE (MySQL 8.0.18+) — ACTUAL execution stats (query waqai chalti hai)
-- ----------------------------------------------------------------------------
EXPLAIN ANALYZE
SELECT e.name, d.name AS department
FROM employees e
JOIN departments d ON e.department_id = d.id
WHERE e.salary > 150000;
-- Output me "actual time" aur "actual rows" milte hain — estimate vs reality
-- compare karne ke liye sabse useful tool hai.

-- ----------------------------------------------------------------------------
-- 4) Extra column ke important warnings
-- ----------------------------------------------------------------------------
-- "Using index"        -> GOOD. Covering index — clustered index tak jane ki zarurat nahi
-- "Using where"          -> Normal, WHERE filter applied
-- "Using filesort"       -> ⚠️ ORDER BY ke liye EXTRA sort step (memory/disk) — index se sort nahi mila
-- "Using temporary"      -> ⚠️ Temp table banani padi (GROUP BY/DISTINCT complex case me) — slow
-- "Using join buffer"    -> ⚠️ JOIN column pe index nahi hai (Block Nested Loop/Hash Join fallback)

CREATE INDEX idx_hire_date ON employees(hire_date);

EXPLAIN SELECT * FROM employees ORDER BY name;        -- "Using filesort" (name pe index nahi)
EXPLAIN SELECT * FROM employees ORDER BY hire_date;   -- filesort nahi (index se sorted milta hai)

-- ----------------------------------------------------------------------------
-- 5) SLOW QUERY LOG — production me slow queries dhoondne ka tareeqa
-- ----------------------------------------------------------------------------
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;          -- 1 second se zyada lene wali queries log hongi
SHOW VARIABLES LIKE 'slow_query_log_file';
-- Production me: mysqldumpslow ya pt-query-digest tool se log analyze karte hain

-- ----------------------------------------------------------------------------
-- 6) N+1 Query Problem (ORM context — Laravel/Eloquent me bohat common)
-- ----------------------------------------------------------------------------
-- ❌ BAD: 1 query for employees + N queries (har employee ke liye department):
--   SELECT * FROM employees;                          -- 1 query
--   foreach employee: SELECT * FROM departments WHERE id = ?;  -- N queries
--
-- ✅ GOOD: JOIN ya eager-load (Laravel: ->with('department')):
SELECT e.*, d.name AS department_name
FROM employees e
LEFT JOIN departments d ON e.department_id = d.id;
-- ya: SELECT * FROM departments WHERE id IN (1,2,3,4);  -- IN clause se 1 query me sab

-- ----------------------------------------------------------------------------
-- 7) SELECT * avoid karo — sirf chahiye columns lo
-- ----------------------------------------------------------------------------
-- ❌ SELECT * FROM employees;          -- saari columns disk se read + network transfer
-- ✅ SELECT id, name, salary FROM employees;  -- kam I/O, covering index ka chance bhi badhta hai

-- ----------------------------------------------------------------------------
-- 8) LIMIT + OFFSET ka PAGINATION PROBLEM (bohat common interview/real issue)
-- ----------------------------------------------------------------------------
-- ❌ BAD: bade OFFSET pe slow ho jata hai (MySQL pehle saari skip ki hui
--    rows bhi internally scan karta hai):
SELECT * FROM employees ORDER BY id LIMIT 20 OFFSET 100000;   -- SLOW on large tables

-- ✅ GOOD: "Keyset Pagination" / "Seek Method" — last seen id ko remember karo:
SELECT * FROM employees WHERE id > 100000 ORDER BY id LIMIT 20;  -- FAST (index seek)

-- ----------------------------------------------------------------------------
-- 9) Optimizer Hints (rarely needed, lekin interview me pucha jata hai)
-- ----------------------------------------------------------------------------
SELECT * FROM employees USE INDEX (idx_dept_salary) WHERE department_id = 1;
SELECT * FROM employees FORCE INDEX (idx_dept_salary) WHERE department_id = 1;
SELECT * FROM employees IGNORE INDEX (idx_dept_salary) WHERE department_id = 1;
-- Use case: jab optimizer GALAT index choose kar le (rare, but stale statistics
-- ki wajah se ho sakta hai) — ANALYZE TABLE employees; statistics refresh karta hai

ANALYZE TABLE employees;   -- index statistics refresh (optimizer ko sahi decision lene me madad)

-- ----------------------------------------------------------------------------
-- 10) Common Optimization Checklist (Production Debugging Order)
-- ----------------------------------------------------------------------------
-- 1. EXPLAIN/EXPLAIN ANALYZE chalao — type=ALL? rows estimate bohat zyada?
-- 2. WHERE/JOIN columns pe index hai? Leftmost prefix rule follow ho rahi hai?
-- 3. SELECT * to nahi kar rahe? Sirf zaruri columns lo
-- 4. Function on indexed column to nahi laga di (WHERE YEAR(date)=...)?
-- 5. N+1 query problem to nahi (ORM eager loading check karo)?
-- 6. LIMIT/OFFSET pagination bade offset pe to nahi (keyset pagination use karo)?
-- 7. JOIN order sahi hai? Chhoti/filtered table pehle?
-- 8. innodb_buffer_pool_size kaafi bada hai (RAM ka 60-70%) production me?
-- 9. Query result CACHE ho sakta hai (Redis) taake DB pe load hi na aaye?

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: Query slow chal rahi hai — debug kaise karoge step by step?
-- A: 1) EXPLAIN/EXPLAIN ANALYZE laga kar type aur rows dekho
--    2) "Using filesort"/"Using temporary"/"Using join buffer" warnings check karo
--    3) Confirm karo zaruri columns pe index hai aur leftmost prefix follow ho raha hai
--    4) SHOW PROCESSLIST se dekho koi lock/contention to nahi
--    5) Slow query log se pattern dekho (kya ye consistently slow hai ya kabhi kabhi)
--
-- Q: "rows" column EXPLAIN me estimate hai ya exact?
-- A: ESTIMATE hai — InnoDB statistics (sampling-based) se calculate hota hai.
--    Exact count ke liye EXPLAIN ANALYZE use karo (actual rows dikhata hai,
--    lekin query waqai EXECUTE hoti hai isme).
--
-- Q: Index hone ke bawajood optimizer Full Table Scan kyun choose karta hai?
-- A: Agar query bohat zyada % rows match kar rahi hai (e.g. >20-30% table),
--    to Full Table Scan ACTUALLY index lookup se SASTA hota hai (random I/O
--    se sequential I/O behtar hai). Optimizer COST-BASED decision leta hai,
--    sirf "index hai to use karo" wala rule nahi follow karta.
