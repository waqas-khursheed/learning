-- ============================================================================
--  MySQL DATA TYPES — Sahi type choose karna = future performance
--  (Wrong data type aksar 6 years baad bhi production me dikhta hai!)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1) NUMERIC TYPES
-- ----------------------------------------------------------------------------
-- | Type          | Size      | Range (signed)                  | Kab use karo |
-- |----------------|-----------|----------------------------------|----------------|
-- | TINYINT        | 1 byte    | -128 to 127                      | status flags, boolean (0/1) |
-- | SMALLINT       | 2 bytes   | -32,768 to 32,767                | small counters |
-- | INT            | 4 bytes   | -2.1B to 2.1B                    | normal IDs, counts |
-- | BIGINT         | 8 bytes   | -9.2 * 10^18                     | high-volume IDs (e.g. transaction ids) |
-- | DECIMAL(M,D)   | variable  | EXACT precision                  | MONEY/PRICE — hamesha ye use karo |
-- | FLOAT/DOUBLE   | 4/8 bytes | approximate                      | scientific calc, MONEY ke liye KABHI NAHI |
--
-- ⚠️ INTERVIEW GOTCHA: Money/price ke liye FLOAT/DOUBLE use mat karo —
-- rounding errors aate hain (0.1 + 0.2 != 0.3 binary floating point me).
-- Hamesha DECIMAL(10,2) use karo jaisa hamare `products.price` me hai.

SELECT 0.1 + 0.2 AS float_problem;        -- DECIMAL me precise, FLOAT me approx

-- UNSIGNED: jab negative value kabhi nahi aayegi (id, price, age) — range double ho jata hai
CREATE TABLE demo_unsigned (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,  -- 0 to 4.29 billion (instead of -2.1B to 2.1B)
    age TINYINT UNSIGNED                          -- 0 to 255
);

-- ----------------------------------------------------------------------------
-- 2) STRING TYPES
-- ----------------------------------------------------------------------------
-- | Type          | Max Size           | Storage                          |
-- |----------------|---------------------|------------------------------------|
-- | CHAR(n)        | 255 chars           | FIXED length (padding ke sath)    |
-- | VARCHAR(n)     | 65,535 bytes        | VARIABLE length (length prefix)   |
-- | TEXT           | 65,535 chars        | Off-page storage possible          |
-- | MEDIUMTEXT     | 16MB                | bade content (articles, json)      |
-- | LONGTEXT       | 4GB                 | bohat bada content                |
-- | ENUM           | -                   | fixed set of values (1-2 bytes)   |
--
-- CHAR vs VARCHAR (CLASSIC INTERVIEW Q):
-- CHAR('AB', 10)    -> hamesha 10 bytes store hota hai (padded with spaces)
-- VARCHAR('AB', 10) -> sirf 2 bytes + 1 length byte store hota hai
--
-- CHAR FASTER hai FIXED-length data ke liye (country_code CHAR(2), status CHAR(1))
-- VARCHAR BEHTAR hai variable-length data ke liye (name, email, address)

-- ENUM example (jaisa hamari employees.status me hai):
-- status ENUM('active','inactive','terminated')
-- Internally ye TINYINT ki tarah store hota hai (1,2,3) — fast aur space efficient
-- Lekin LIMITATION: naya value add karne ke liye ALTER TABLE chahiye hota hai.
-- Isliye agar values frequently change hongi to ENUM ki bajaye lookup table use karo.

-- ----------------------------------------------------------------------------
-- 3) DATE/TIME TYPES
-- ----------------------------------------------------------------------------
-- | Type        | Format                  | Range/Use Case                       |
-- |--------------|--------------------------|----------------------------------------|
-- | DATE         | YYYY-MM-DD              | birth_date, hire_date                |
-- | TIME         | HH:MM:SS                | duration, schedule time               |
-- | DATETIME     | YYYY-MM-DD HH:MM:SS     | fixed value, TIMEZONE AWARE NAHI hota |
-- | TIMESTAMP    | YYYY-MM-DD HH:MM:SS     | UTC me store, auto-update support     |
-- | YEAR         | YYYY                    | rarely used                           |
--
-- DATETIME vs TIMESTAMP (CLASSIC INTERVIEW Q):
-- - TIMESTAMP: 4 bytes, range 1970–2038, UTC me store hota hai aur connection
--   timezone ke hisab se convert hota hai. created_at/updated_at ke liye best.
-- - DATETIME: 8 bytes (5 in MySQL 5.6+), range 1000–9999, NO timezone conversion.
-- - TIMESTAMP me "ON UPDATE CURRENT_TIMESTAMP" feature milta hai (auto updated_at)

CREATE TABLE demo_datetime (
    id INT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------------------------
-- 4) JSON TYPE (MySQL 5.7+)
-- ----------------------------------------------------------------------------
CREATE TABLE demo_json (
    id INT PRIMARY KEY,
    metadata JSON
);
INSERT INTO demo_json VALUES (1, '{"tags": ["sale", "new"], "rating": 4.5}');

-- JSON query karna:
SELECT metadata->>'$.rating' AS rating FROM demo_json;
SELECT JSON_EXTRACT(metadata, '$.tags[0]') AS first_tag FROM demo_json;

-- Functional index JSON field pe (search fast karne ke liye):
ALTER TABLE demo_json ADD COLUMN rating_virtual DECIMAL(3,1)
    GENERATED ALWAYS AS (metadata->>'$.rating') VIRTUAL;
CREATE INDEX idx_rating_virtual ON demo_json (rating_virtual);

-- ----------------------------------------------------------------------------
-- 5) BOOLEAN
-- ----------------------------------------------------------------------------
-- MySQL me real BOOLEAN type NAHI hai — ye TINYINT(1) ka alias hai.
-- TRUE = 1, FALSE = 0

-- ----------------------------------------------------------------------------
-- 6) NULL vs Empty String vs 0 (INTERVIEW GOTCHA)
-- ----------------------------------------------------------------------------
-- NULL = "value unknown / absent"  (NOT same as 0 or '')
-- NULL ke sath comparison (=, !=) hamesha NULL return karta hai, TRUE/FALSE nahi:
SELECT NULL = NULL;       -- Result: NULL (not TRUE!)
SELECT NULL IS NULL;      -- Result: 1 (TRUE) — yehi sahi tareeqa hai

-- Aggregate functions NULL ko IGNORE karte hain:
SELECT AVG(salary) FROM employees;  -- NULL salaries count nahi hongi denominator me

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: VARCHAR(255) hamesha use karna chahiye?
-- A: Nahi — column ki actual zarurat ke hisab se size do. Index size aur
--    memory usage column length se affect hota hai (VARCHAR(255) me bhi
--    sirf actual data + 1-2 bytes store hota hai, lekin sorting/temp tables
--    me worst-case length allocate ho sakti hai).
--
-- Q: Primary key ke liye INT use karu ya BIGINT?
-- A: Agar rows 2 billion se kam rahenge to INT UNSIGNED kaafi hai
--    (4 bytes, 0 to ~4.29B). High-growth systems (logs, events) me BIGINT
--    shuru se use karo — baad me migrate karna mushkil hota hai.
--
-- Q: UUID ko primary key banana chahiye?
-- A: Trade-off hai — UUID globally unique hai (distributed systems, no
--    collision) lekin RANDOM hone ki wajah se InnoDB clustered index
--    fragmentation badhti hai (random insert position). Agar UUID chahiye
--    to UUID v7 / ULID use karo (time-ordered) ya BIGINT auto_increment +
--    separate UUID column (unique key) rakho.
