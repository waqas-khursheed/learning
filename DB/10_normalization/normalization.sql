-- ============================================================================
--  NORMALIZATION (1NF - 5NF) & DENORMALIZATION TRADEOFFS
--  Schema design interview rounds me ye topic bohat poocha jata hai
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Normalization kya hai?
-- ----------------------------------------------------------------------------
-- Data ko ALAG ALAG tables me todna taake DUPLICATION (redundancy) kam ho
-- aur UPDATE/INSERT/DELETE ANOMALIES na aayein.

-- ----------------------------------------------------------------------------
-- BAD EXAMPLE (Unnormalized) — sab kuch ek hi table me
-- ----------------------------------------------------------------------------
-- orders_bad (
--   order_id, customer_name, customer_email, customer_city,
--   product_name, product_price, quantity
-- )
-- Problem: Agar customer Ali Hamza ka email change ho, to uski SAARI orders
-- wali rows me update karna padega (Update Anomaly). Agar Ali ki saari
-- orders delete ho jayein, uski info bhi gayab (Delete Anomaly). Naya
-- customer add nahi kar sakte jab tak order na ho (Insert Anomaly).

-- ----------------------------------------------------------------------------
-- 1NF — First Normal Form
-- ----------------------------------------------------------------------------
-- RULE: Har column me ATOMIC (single) value ho — koi comma-separated list
-- ya repeating group nahi.
--
-- ❌ VIOLATION:
-- employees_bad (id, name, phone_numbers)
--   1, 'Ali', '0300-1111111, 0321-2222222'   <- multiple values ek column me
--
-- ✅ FIX: alag table banao
CREATE TABLE employee_phones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    phone_number VARCHAR(20) NOT NULL
);
-- Ab har row me sirf EK phone number — 1NF satisfy

-- ----------------------------------------------------------------------------
-- 2NF — Second Normal Form
-- ----------------------------------------------------------------------------
-- RULE: 1NF + koi PARTIAL DEPENDENCY na ho (sirf COMPOSITE PRIMARY KEY
-- wale tables pe apply hota hai). Har non-key column PURI primary key pe
-- depend hona chahiye, sirf USKE EK PART pe nahi.
--
-- ❌ VIOLATION: order_items (order_id, product_id, product_name, quantity)
--   PK = (order_id, product_id). Lekin product_name sirf product_id pe
--   depend karta hai, order_id pe nahi -> PARTIAL DEPENDENCY
--
-- ✅ FIX: product_name ko products table me rakho, order_items me sirf product_id rakho
-- (yehi hamare company_db schema me already hai — products alag table hai)

-- ----------------------------------------------------------------------------
-- 3NF — Third Normal Form
-- ----------------------------------------------------------------------------
-- RULE: 2NF + koi TRANSITIVE DEPENDENCY na ho. Non-key column kisi DOOSRE
-- non-key column pe depend nahi hona chahiye (sirf PK pe depend ho).
--
-- ❌ VIOLATION: employees_bad (id, name, department_id, department_name, department_location)
--   department_name aur department_location, department_id pe depend
--   karte hain — id pe NAHI (TRANSITIVE: id -> department_id -> department_name)
--
-- ✅ FIX: departments ko alag table banao (yehi hamare schema me hai):
--   employees (id, name, department_id)  -- department_id FK
--   departments (id, name, location)
-- Ab department_name update karna ho to SIRF EK row update karni padti hai

-- ----------------------------------------------------------------------------
-- BCNF — Boyce-Codd Normal Form (3NF ka stricter version)
-- ----------------------------------------------------------------------------
-- RULE: Har DETERMINANT (jo column doosre column ko determine karta hai)
-- ek CANDIDATE KEY hona chahiye. Edge cases handle karta hai jahan 3NF
-- kaafi nahi hota (multiple overlapping candidate keys).
-- Real-world me rare hi explicitly discuss hota hai, mostly 3NF tak design kaafi hai.

-- ----------------------------------------------------------------------------
-- 4NF — Fourth Normal Form
-- ----------------------------------------------------------------------------
-- RULE: MULTI-VALUED DEPENDENCY nahi honi chahiye (do INDEPENDENT
-- multi-valued facts ek table me nahi hone chahiye).
--
-- ❌ VIOLATION: employee_skills_languages (employee_id, skill, language)
--   Agar Ahmed ke 2 skills aur 2 languages hain, to 2x2 = 4 rows banengi
--   (Cartesian-like) — skills aur languages EK DOOSRE se UNRELATED hain.
--
-- ✅ FIX: alag tables
CREATE TABLE employee_skills (employee_id INT, skill VARCHAR(50));
CREATE TABLE employee_languages (employee_id INT, language VARCHAR(50));

-- ----------------------------------------------------------------------------
-- 5NF — Fifth Normal Form (Project-Join Normal Form)
-- ----------------------------------------------------------------------------
-- RULE: Table ko chhote tables me todo agar JOIN se original data LOSSLESS
-- reconstruct ho sake, aur koi REDUNDANCY na bache. Extremely rare practical
-- use-case (complex many-to-many-to-many relationships). Interview me sirf
-- THEORY level pe pucha jata hai, real design me 3NF/BCNF tak kaafi hota hai.

-- ============================================================================
-- DENORMALIZATION — Jaan-bujh kar redundancy add karna (PERFORMANCE ke liye)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Kyun karte hain?
-- ----------------------------------------------------------------------------
-- Normalized schema = data integrity BEHTAR, lekin READ queries me ZYADA
-- JOINS lagte hain (slower, especially read-heavy / reporting / analytics
-- systems me). Denormalization READ PERFORMANCE ke liye redundancy
-- jaan-bujh kar add karta hai.

-- Example: orders table me total_price COLUMN already STORE hai (calculate
-- nahi karte quantity * product.price se har baar) — ye DENORMALIZATION hai!
-- Trade-off: agar product price change ho jaye, purani orders ka total_price
-- WAHI rahega (jo CORRECT hai — historical record), lekin agar price ko
-- LIVE calculate karte to bug ban jata.

-- Common denormalization techniques:
-- 1. Redundant columns (jaise total_price upar)
-- 2. Summary/aggregate tables (daily_sales_summary — pre-calculated)
-- 3. Materialized views (kuch DBs me native support, MySQL me manually
--    table + scheduled job/trigger se maintain karte hain)

CREATE TABLE daily_sales_summary (
    sale_date DATE PRIMARY KEY,
    total_orders INT,
    total_revenue DECIMAL(12,2)
);
-- Har raat ek cron job/event se ye table update hoti hai — dashboard query
-- ab heavy JOIN+GROUP BY ki bajaye sirf 1 row read karti hai (super fast)

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: Normalization vs Denormalization — kab kya choose karoge?
-- A: OLTP systems (transactional, frequent writes, data integrity critical —
--    banking, e-commerce orders) -> NORMALIZED (3NF tak).
--    OLAP/Reporting/Analytics systems (read-heavy, complex aggregations,
--    dashboards) -> DENORMALIZED ya hybrid (normalized core + denormalized
--    summary/reporting tables).
--    Real-world: zyada tar systems HYBRID hote hain — core data normalized,
--    kuch specific performance-critical columns/tables denormalized.
--
-- Q: 3NF tak normalize karna hamesha best practice hai?
-- A: General guideline hai, lekin "it depends" — agar JOIN overhead query
--    performance ko hurt kar raha hai aur data rarely changes (e.g.
--    product category name), thora denormalization reasonable trade-off
--    ho sakta hai. Engineering judgment call hai, dogma nahi.
--
-- Q: total_price ko orders table me store karna normalization violate karta hai?
-- A: Technically YES (transitive dependency — calculated from quantity *
--    price), lekin practically YEHI SAHI design hai kyunki ye HISTORICAL
--    record hai (order ke waqt ka price), na ki current product price ka
--    reflection. Normalization rules ko BLINDLY follow nahi karte —
--    business requirement (audit trail) ko priority do.
