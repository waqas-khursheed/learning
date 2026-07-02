-- ============================================================================
--  KEYS & CONSTRAINTS — Data Integrity ka Foundation
-- ============================================================================
USE company_db;

-- ----------------------------------------------------------------------------
-- 1) PRIMARY KEY
-- ----------------------------------------------------------------------------
-- - Har row ko UNIQUELY identify karta hai
-- - NULL allowed NAHI
-- - Har table me sirf EK primary key (lekin composite ho sakti hai — multiple columns)
-- - InnoDB me Primary Key = CLUSTERED INDEX (actual data isi order me disk pe store hota hai)
--   -> Yehi wajah hai ke PK choice INDEX performance ko directly affect karti hai (07_indexes/ me detail)

CREATE TABLE demo_pk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

-- Composite Primary Key (jab do columns mil kar uniqueness define karein):
CREATE TABLE order_items (
    order_id    INT UNSIGNED NOT NULL,
    product_id  INT UNSIGNED NOT NULL,
    quantity    INT NOT NULL DEFAULT 1,
    PRIMARY KEY (order_id, product_id)   -- ek order me ek product sirf ek hi row me ho
);

-- ----------------------------------------------------------------------------
-- 2) FOREIGN KEY
-- ----------------------------------------------------------------------------
-- - Doosri table ke Primary/Unique Key ko REFERENCE karti hai
-- - REFERENTIAL INTEGRITY enforce karti hai (orphan rows nahi ban sakti)
-- - Sirf InnoDB me support hai (MyISAM me NAHI)

-- Hamari employees table me already 2 FK hain:
--   department_id -> departments.id
--   manager_id    -> employees.id (self-referencing)

-- ON DELETE / ON UPDATE actions:
-- | Action      | Behaviour                                                  |
-- |--------------|--------------------------------------------------------------|
-- | CASCADE      | Parent delete/update -> child me bhi automatically ho jata  |
-- | SET NULL     | Parent delete -> child ka FK column NULL ho jata hai        |
-- | RESTRICT     | Parent delete BLOCK ho jati hai agar child rows exist karein |
-- | NO ACTION    | RESTRICT jaisa hi (MySQL me almost same)                    |

-- Example: department delete ho to employees.department_id automatically NULL ho (already schema me hai)
-- Example: customer delete ho to uske saare orders bhi CASCADE delete ho jayein
-- Example: product delete ho to RESTRICT (agar orders me use ho raha hai to delete fail ho)

DELETE FROM departments WHERE id = 999; -- non-existent, demo ke liye safe

-- Try karke dekho ye FAIL hoga kyunki product id=1 orders me use ho raha hai (RESTRICT):
-- DELETE FROM products WHERE id = 1;
-- ERROR 1451: Cannot delete or update a parent row: a foreign key constraint fails

-- ----------------------------------------------------------------------------
-- 3) UNIQUE KEY / CONSTRAINT
-- ----------------------------------------------------------------------------
-- - Duplicate values allow nahi karta, lekin NULL multiple baar allow hota hai (PK ke ulat)
-- - Multiple UNIQUE keys ek table me ho sakti hain (PK sirf ek)
-- - Internally ek INDEX bhi create hoti hai (unique index)

ALTER TABLE employees ADD CONSTRAINT uq_employee_email UNIQUE (email); -- already unique tha
-- email already UNIQUE hai schema me

-- Composite unique constraint:
CREATE TABLE demo_unique (
    user_id INT,
    course_id INT,
    UNIQUE KEY uq_user_course (user_id, course_id)  -- ek user ek course me sirf 1 baar enroll ho
);

-- ----------------------------------------------------------------------------
-- 4) NOT NULL
-- ----------------------------------------------------------------------------
-- Column me NULL value allow nahi. Default = NULL allowed hota hai agar specify na karo.
-- ALTER TABLE employees MODIFY salary DECIMAL(10,2) NOT NULL; (already hai)

-- ----------------------------------------------------------------------------
-- 5) DEFAULT
-- ----------------------------------------------------------------------------
-- Value specify na karne par automatic value use hoti hai
-- status ENUM(...) DEFAULT 'active' (already employees me hai)

-- ----------------------------------------------------------------------------
-- 6) CHECK Constraint (MySQL 8.0.16+)
-- ----------------------------------------------------------------------------
ALTER TABLE employees ADD CONSTRAINT chk_salary_positive CHECK (salary > 0);
ALTER TABLE products ADD CONSTRAINT chk_price_positive CHECK (price >= 0);

-- Try this -> fails because of CHECK constraint:
-- INSERT INTO employees (name, email, salary, hire_date) VALUES ('Test', 'test@x.com', -5000, '2024-01-01');
-- ERROR 3819: Check constraint 'chk_salary_positive' is violated.

-- ----------------------------------------------------------------------------
-- 7) AUTO_INCREMENT
-- ----------------------------------------------------------------------------
-- Har INSERT pe automatically value badhti hai. Sirf ek column per table,
-- aur wo column kisi key (usually PK) ka part hona chahiye.
SELECT LAST_INSERT_ID();   -- last auto-increment value jo CURRENT session me insert hui

-- ----------------------------------------------------------------------------
-- 8) CANDIDATE KEY vs PRIMARY KEY vs ALTERNATE KEY vs SUPER KEY (THEORY — interview classic)
-- ----------------------------------------------------------------------------
-- - Super Key: Koi bhi column(s) ka combination jo row ko uniquely identify kare
--   (extra/unnecessary columns ke sath bhi)
-- - Candidate Key: Minimal Super Key (koi extra column nahi) — multiple ho sakti hain
--   (e.g. employees me `id` aur `email` dono candidate keys hain)
-- - Primary Key: Jo candidate key ko hum FINAL select karte hain as the main identifier
-- - Alternate Key: Baqi candidate keys jo PK nahi bani (e.g. `email` agar PK `id` hai)

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: PRIMARY KEY aur UNIQUE KEY me kya farq hai?
-- A: 1) PK NULL allow nahi karti, UNIQUE multiple NULL allow karti hai (MySQL me)
--    2) Ek table me sirf 1 PK, multiple UNIQUE keys ho sakti hain
--    3) PK InnoDB me clustered index banati hai (data isi order me store hota
--       hai), UNIQUE sirf secondary index banati hai
--
-- Q: Composite key kab use karte ho?
-- A: Jab single column uniqueness define nahi kar sakta — e.g. order_items
--    table me (order_id, product_id) mil kar row unique banate hain.
--
-- Q: Foreign key na lagayein to kya hoga (sirf application logic se control karein)?
-- A: Performance thori better lag sakti hai (FK check overhead nahi),
--    lekin DATA INTEGRITY risk pe aa jati hai — orphan records, race
--    conditions me inconsistent data ban sakta hai. High-scale systems
--    (sharded/microservices) me kabhi kabhi FK jaan-bujh kar skip karte
--    hain aur integrity application/queue level pe handle karte hain.
