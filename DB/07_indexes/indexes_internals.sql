-- ============================================================================
--  INDEXES — B+Tree Internals (SABSE ZYADA poocha jane wala deep topic)
-- ============================================================================
USE company_db;

-- ----------------------------------------------------------------------------
-- 1) Index kya hai aur kyun zaruri hai?
-- ----------------------------------------------------------------------------
-- Index ek ALAG data structure hai jo column(s) ki values ko SORTED order me
-- rakhta hai, taake WHERE/JOIN/ORDER BY full table scan (O(n)) ki bajaye
-- fast lookup (O(log n)) kar sakein.
--
-- BINA INDEX:  WHERE email = 'x' -> MySQL har row check karta hai (Full Table Scan)
-- INDEX HONE PAR: B+Tree me binary-search jaisa traversal -> bohat fast

-- ----------------------------------------------------------------------------
-- 2) B+TREE — MySQL (InnoDB) ka default index structure
-- ----------------------------------------------------------------------------
-- B+Tree kyun, Hash Table kyun nahi? Kyunki B+Tree SORTED hota hai —
-- isse RANGE queries (>, <, BETWEEN, ORDER BY) efficient hoti hain.
-- Hash index sirf EXACT match (=) ke liye fast hai, range queries ke liye useless.
--
--                        [ROOT — internal node, keys + pointers]
--                       /        |         \
--              [internal]   [internal]   [internal]     <- non-leaf nodes
--              /   |   \       ...
--        [leaf]->[leaf]->[leaf]->[leaf]->[leaf]   <- LEAF NODES (linked list!)
--         (10,20) (30,40) (50,60) ...
--
-- KEY FACTS (interview gold):
-- - Leaf nodes EK DOOSRE se LINKED LIST ki tarah connected hote hain
--   -> range scan (BETWEEN, >, ORDER BY) bohat fast hota hai, sequential read
-- - Tree HAMESHA BALANCED rehta hai -> har lookup approx SAME depth me hota
--   hai (typically 3-4 levels even for millions of rows)
-- - Height = O(log n) -> 1 million rows ke liye bhi sirf ~3-4 disk reads lagte hain

-- ----------------------------------------------------------------------------
-- 3) CLUSTERED INDEX vs SECONDARY (NON-CLUSTERED) INDEX — InnoDB SPECIFIC
-- ----------------------------------------------------------------------------
-- CLUSTERED INDEX:
-- - InnoDB me PRIMARY KEY HAMESHA clustered index hoti hai (no choice!)
-- - Leaf nodes me ACTUAL ROW DATA store hota hai (na ki sirf pointer)
-- - Table ka data DISK PE PHYSICALLY PK ke order me arranged hota hai
-- - Har table ki sirf EK clustered index ho sakti hai (kyunki data ek hi
--   physical order me ho sakta hai)
-- - Agar koi PK define na karo, InnoDB khud ek hidden 6-byte ROW_ID column
--   bana kar use clustered index banata hai
--
-- SECONDARY INDEX (jo bhi ALTER TABLE ADD INDEX se banao):
-- - Leaf nodes me actual row data NAHI hota — sirf the PRIMARY KEY VALUE store hota hai
-- - Lookup do step me hota hai:
--     1) Secondary index B+Tree traverse karo -> PK value milti hai
--     2) Us PK se Clustered index traverse karo -> actual row milti hai
--   Isay "BOOKMARK LOOKUP" ya "Double Lookup" kehte hain
--
-- ⚠️ YEHI WAJAH HAI ke secondary index se fetch, clustered index se fetch
-- se THORA SLOWER hota hai (2 B+Tree traversal vs 1).

-- Demo: employees.id (PK) = clustered. employees.email (UNIQUE) = secondary.
SELECT * FROM employees WHERE id = 3;        -- 1 B+Tree lookup (clustered, direct)
SELECT * FROM employees WHERE email = 'sara@company.com';  -- 2 lookups (secondary -> PK -> clustered)

-- ----------------------------------------------------------------------------
-- 4) COVERING INDEX — Jab secondary index hi sab kuch de de (double lookup avoid)
-- ----------------------------------------------------------------------------
-- Agar query me jin columns ki zarurat hai wo SAARE index me hi maujood hain,
-- to InnoDB ko clustered index tak jaane ki zarurat hi nahi (no bookmark lookup).

CREATE INDEX idx_dept_salary ON employees (department_id, salary);

-- Ye query COVERED hai (department_id aur salary dono index me hain):
EXPLAIN SELECT department_id, salary FROM employees WHERE department_id = 1;
-- EXPLAIN output me "Using index" dikhega -> covering index use hua, fast!

-- Ye query COVERED NAHI hai (name index me nahi hai -> clustered index tak jana padega):
EXPLAIN SELECT department_id, name FROM employees WHERE department_id = 1;

-- ----------------------------------------------------------------------------
-- 5) COMPOSITE INDEX & LEFTMOST PREFIX RULE (SABSE ZYADA GALTI YAHAN HOTI HAI)
-- ----------------------------------------------------------------------------
-- idx_dept_salary (department_id, salary) -> column ORDER matter karta hai!
--
-- Ye index in queries me use HOGI:
EXPLAIN SELECT * FROM employees WHERE department_id = 1;                       -- YES (leftmost column)
EXPLAIN SELECT * FROM employees WHERE department_id = 1 AND salary > 100000;   -- YES (both, in order)
EXPLAIN SELECT * FROM employees WHERE department_id = 1 ORDER BY salary;       -- YES (sort bhi index se mil jata hai)

-- Ye index in queries me USE NAHI HOGI (ya partial use hogi):
EXPLAIN SELECT * FROM employees WHERE salary > 100000;        -- NO! (department_id skip kiya — leftmost prefix break)
EXPLAIN SELECT * FROM employees WHERE salary = 175000;        -- NO! same reason

-- RULE: Composite index (a, b, c) sirf tab use hoti hai jab query WHERE me
-- "a" ho, ya "a AND b", ya "a AND b AND c" — lekin agar "a" SKIP karke
-- seedha "b" ya "c" pe filter lagao to index USE NAHI hoti (ya bohat
-- inefficient "index skip scan" hota hai, jo MySQL 8.0 ke kuch cases me hi support hai).

-- ----------------------------------------------------------------------------
-- 6) Index Cardinality (uniqueness) — kis column pe index lagani chahiye
-- ----------------------------------------------------------------------------
-- High Cardinality (bohat unique values — email, id) -> index BOHAT effective
-- Low Cardinality (kam unique values — gender, boolean status flag) ->
--   index ka fayda KAM hota hai (optimizer kabhi index IGNORE bhi kar deta
--   hai aur Full Table Scan choose kar leta hai agar matching rows 20-30%+ hon)

SHOW INDEX FROM employees;   -- Cardinality column dekho

-- ----------------------------------------------------------------------------
-- 7) Index kab USE NAHI hoti (COMMON MISTAKES — interview classic)
-- ----------------------------------------------------------------------------

-- a) Function/Expression column pe lagana (index ignore ho jati hai):
EXPLAIN SELECT * FROM employees WHERE YEAR(hire_date) = 2020;       -- BAD: function on column
EXPLAIN SELECT * FROM employees WHERE hire_date >= '2020-01-01' AND hire_date < '2021-01-01';  -- GOOD: sargable

-- b) Leading wildcard LIKE (prefix unknown -> B+Tree traverse nahi ho sakta):
EXPLAIN SELECT * FROM employees WHERE name LIKE '%Ali';    -- BAD: leading %
EXPLAIN SELECT * FROM employees WHERE name LIKE 'Sara%';   -- GOOD: trailing % chalta hai

-- c) Implicit type conversion (different data type compare):
-- EXPLAIN SELECT * FROM employees WHERE email = 12345;   -- BAD: string column, number compare -> cast hoga, index miss

-- d) OR with non-indexed column / different indexes:
EXPLAIN SELECT * FROM employees WHERE department_id = 1 OR name = 'Sara Ali';
-- OR me agar dono sides indexed na hon to optimizer Full Table Scan kar sakta hai

-- e) NOT IN, != , <> -> kabhi kabhi index use nahi hoti (negative conditions
--    me bohat saari rows match karti hain, optimizer scan ko hi behtar samajhta hai)

-- ----------------------------------------------------------------------------
-- 8) Index Types Summary
-- ----------------------------------------------------------------------------
-- | Type              | Use Case                                            |
-- |---------------------|--------------------------------------------------------|
-- | PRIMARY (Clustered)  | Automatic on PK, data physically sorted             |
-- | UNIQUE               | Uniqueness enforce + fast lookup                    |
-- | Regular (Secondary)   | General WHERE/JOIN speed up                        |
-- | Composite             | Multi-column filters (leftmost prefix rule yaad rakho) |
-- | FULLTEXT              | Text search (MATCH AGAINST) — articles, descriptions |
-- | Spatial (R-Tree)      | Geo data (POINT, POLYGON)                          |

CREATE FULLTEXT INDEX idx_product_name_fulltext ON products(name);
SELECT * FROM products WHERE MATCH(name) AGAINST('laptop' IN NATURAL LANGUAGE MODE);

-- ----------------------------------------------------------------------------
-- 9) Index ka COST bhi hota hai (sirf benefit nahi)
-- ----------------------------------------------------------------------------
-- - Har INSERT/UPDATE/DELETE pe SAARI indexes bhi update honi padti hain
--   -> write-heavy tables pe zyada indexes WRITE PERFORMANCE slow karti hain
-- - Disk space lagti hai (har index apni B+Tree copy maintain karta hai)
-- - Isliye: "jitni queries utni indexes mat banao" — sirf un columns pe
--   index banao jo WHERE/JOIN/ORDER BY me FREQUENTLY use hote hain.

-- ----------------------------------------------------------------------------
-- 10) Adaptive Hash Index (AHI) — InnoDB internal optimization
-- ----------------------------------------------------------------------------
-- InnoDB khud detect karta hai ke konse B+Tree paths frequently access ho
-- rahe hain, aur unke liye automatically RAM me ek HASH INDEX bana deta hai
-- (transparent, manually control nahi karte). Ye sirf exact-match lookups
-- ko O(1) tak speed up karta hai. SHOW VARIABLES LIKE 'innodb_adaptive_hash_index';

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: Clustered vs Non-Clustered (Secondary) Index me farq?
-- A: Clustered index ki leaf nodes me ACTUAL DATA hota hai (PK = clustered
--    in InnoDB, table data isi order me disk pe hota hai). Secondary index
--    ki leaf nodes me sirf PK VALUE hota hai -> row chahiye to ek extra
--    lookup (clustered index me) lagta hai.
--
-- Q: Composite index (a,b,c) banayi — kya ye WHERE b=x query me kaam aayegi?
-- A: Nahi, Leftmost Prefix Rule ki wajah se. Index sirf tab use hoti hai
--    jab leftmost column (a) bhi WHERE me ho.
--
-- Q: B+Tree hi kyun, Binary Search Tree (BST) kyun nahi?
-- A: BST height O(log2 n) hoti hai lekin HAR NODE me sirf 1 key — disk se
--    bohat zyada I/O reads lagenge. B+Tree node (page, typically 16KB) me
--    SAINKARON keys hoti hain -> tree ki height bohat kam (3-4 levels for
--    millions of rows) -> bohat kam disk I/O. B+Tree disk-based storage ke
--    liye specifically design hua hai.
--
-- Q: Kitni indexes ek table pe honi chahiye?
-- A: Koi fixed number nahi — depend karta hai read:write ratio pe. Read-heavy
--    table (reporting) -> zyada indexes fine. Write-heavy table (logs,
--    events) -> kam indexes (sirf jo zaruri hon), kyunki har write pe index
--    maintenance cost lagti hai.
--
-- Q: EXPLAIN me "Using filesort" aur "Using temporary" dekhna kyun bura hai?
-- A: Matlab MySQL ko ORDER BY/GROUP BY ke liye EXTRA SORT operation (disk/
--    memory pe) karni padi kyunki index se directly sorted order nahi mil
--    saka. Agar ORDER BY column composite index ka part ho (sahi order me)
--    to ye avoid ho sakta hai. Detail: 08_query_optimization/
