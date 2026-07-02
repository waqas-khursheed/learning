-- ============================================================================
--  TRIGGERS & STORED FUNCTIONS
--  (Stored_Procedure.php aur View.php isi folder me already maujood hain)
-- ============================================================================
USE company_db;

-- ----------------------------------------------------------------------------
-- 1) TRIGGER kya hai?
-- ----------------------------------------------------------------------------
-- Trigger ek automatic action hai jo kisi table pe INSERT/UPDATE/DELETE
-- hone par AUTOMATICALLY chalta hai — koi explicit CALL nahi karna padta.
--
-- Types: BEFORE INSERT, AFTER INSERT, BEFORE UPDATE, AFTER UPDATE,
--        BEFORE DELETE, AFTER DELETE  (har table pe ek hi type ka 1 trigger MySQL 5.7 tak,
--        8.0+ me multiple triggers same event pe allowed hain with FOLLOWS/PRECEDES)

-- ----------------------------------------------------------------------------
-- 2) Example: Audit Log Trigger (BOHAT COMMON real-world use-case)
-- ----------------------------------------------------------------------------
CREATE TABLE salary_audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT,
    old_salary DECIMAL(10,2),
    new_salary DECIMAL(10,2),
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DELIMITER $$

CREATE TRIGGER trg_salary_audit
AFTER UPDATE ON employees
FOR EACH ROW
BEGIN
    IF OLD.salary != NEW.salary THEN
        INSERT INTO salary_audit_log (employee_id, old_salary, new_salary)
        VALUES (OLD.id, OLD.salary, NEW.salary);
    END IF;
END$$

DELIMITER ;

-- Test karo:
UPDATE employees SET salary = 200000 WHERE id = 3;
SELECT * FROM salary_audit_log;   -- automatically ek row aa gayi hogi

-- ----------------------------------------------------------------------------
-- 3) Example: BEFORE INSERT — Validation / Auto-fill (data save hone se PEHLE)
-- ----------------------------------------------------------------------------
DELIMITER $$

CREATE TRIGGER trg_validate_salary
BEFORE INSERT ON employees
FOR EACH ROW
BEGIN
    IF NEW.salary < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Salary cannot be negative';
    END IF;

    IF NEW.email NOT LIKE '%@%' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid email format';
    END IF;
END$$

DELIMITER ;

-- Test karo (ye FAIL hoga trigger ki wajah se):
-- INSERT INTO employees (name, email, salary, hire_date) VALUES ('Test', 'bad-email', 50000, '2024-01-01');
-- ERROR 1644: Invalid email format

-- ----------------------------------------------------------------------------
-- 4) Example: AFTER DELETE — Soft Delete pattern / Archive table
-- ----------------------------------------------------------------------------
CREATE TABLE employees_archive LIKE employees;

DELIMITER $$

CREATE TRIGGER trg_archive_deleted_employee
BEFORE DELETE ON employees
FOR EACH ROW
BEGIN
    INSERT INTO employees_archive
    SELECT OLD.*;
END$$

DELIMITER ;
-- Ab koi employee delete ho to uska record archive table me copy ho jata hai
-- automatically, bina application code me extra logic likhe.

-- ----------------------------------------------------------------------------
-- 5) Example: Auto-update stock on order (denormalized data maintain karna)
-- ----------------------------------------------------------------------------
DELIMITER $$

CREATE TRIGGER trg_reduce_stock_after_order
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    UPDATE products SET stock = stock - NEW.quantity WHERE id = NEW.product_id;
END$$

DELIMITER ;

-- ----------------------------------------------------------------------------
-- 6) Trigger Drop / List
-- ----------------------------------------------------------------------------
SHOW TRIGGERS FROM company_db;
DROP TRIGGER IF EXISTS trg_validate_salary;

-- ============================================================================
-- STORED FUNCTIONS — Procedure se farq: FUNCTION hamesha EK VALUE return
-- karti hai aur SELECT statement ke ANDAR use ho sakti hai (procedure nahi ho sakti)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 7) Example: Simple calculation function
-- ----------------------------------------------------------------------------
DELIMITER $$

CREATE FUNCTION calculate_bonus(p_salary DECIMAL(10,2), p_years INT)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE v_bonus DECIMAL(10,2);

    IF p_years >= 5 THEN
        SET v_bonus = p_salary * 0.20;
    ELSEIF p_years >= 2 THEN
        SET v_bonus = p_salary * 0.10;
    ELSE
        SET v_bonus = p_salary * 0.05;
    END IF;

    RETURN v_bonus;
END$$

DELIMITER ;

-- Use directly in SELECT (yehi PROCEDURE se bara farq hai):
SELECT
    name,
    salary,
    TIMESTAMPDIFF(YEAR, hire_date, CURDATE()) AS years_worked,
    calculate_bonus(salary, TIMESTAMPDIFF(YEAR, hire_date, CURDATE())) AS bonus
FROM employees;

-- ----------------------------------------------------------------------------
-- 8) FUNCTION vs PROCEDURE (CLASSIC INTERVIEW Q)
-- ----------------------------------------------------------------------------
-- | Feature              | FUNCTION                      | PROCEDURE                  |
-- |------------------------|----------------------------------|---------------------------------|
-- | Return value           | HAMESHA EK value return karti hai | 0, 1, ya multiple result sets    |
-- | SELECT me use           | YES — SELECT fn(x) FROM table     | NO — sirf CALL se chalta hai     |
-- | Parameters               | Sirf IN (input) parameters         | IN, OUT, INOUT sab support       |
-- | DML allowed (INSERT/UPDATE)| LIMITED/Discouraged (DETERMINISTIC ke sath issues) | YES, freely         |
-- | Transaction control      | Allowed nahi (COMMIT/ROLLBACK)     | Allowed hai                      |

-- ----------------------------------------------------------------------------
-- 9) DETERMINISTIC vs NOT DETERMINISTIC
-- ----------------------------------------------------------------------------
-- DETERMINISTIC: same input -> HAMESHA same output (e.g. math calculation)
-- NOT DETERMINISTIC: output change ho sakta hai (e.g. NOW(), RAND() use kare)
-- MySQL replication/binary logging ke liye ye flag zaruri hota hai (warning
-- aati hai agar function NOT DETERMINISTIC ho aur tum DETERMINISTIC mark karo)

-- ----------------------------------------------------------------------------
-- 10) Trigger ke Pros/Cons (Production Perspective)
-- ----------------------------------------------------------------------------
-- ✅ Pros: Data integrity GUARANTEED hoti hai (chahe koi bhi application/
--    script database access kare, trigger HAMESHA chalega — application
--    layer pe depend nahi karta)
-- ❌ Cons: "Hidden logic" — application code dekh kar pata nahi chalta ke
--    trigger bhi kuch kar raha hai (debugging mushkil ho jati hai).
--    Performance overhead bhi hota hai (har INSERT/UPDATE pe extra work).
--    Large teams me triggers OVERUSE karna maintenance nightmare ban sakta hai.
--
-- BEST PRACTICE: Triggers sirf CRITICAL data-integrity/audit cases ke liye
-- use karo (jaise audit log, denormalized counter maintain karna). Business
-- logic (jaise email bhejna, notification) APPLICATION layer me hi rakho.

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: Trigger aur Stored Procedure me basic farq?
-- A: Trigger AUTOMATICALLY fire hota hai (event-based — INSERT/UPDATE/
--    DELETE pe), explicitly call nahi karte. Procedure explicitly CALL
--    karte ho (manual invocation).
--
-- Q: BEFORE aur AFTER trigger me kab kya use karoge?
-- A: BEFORE -> Validation, ya NEW values modify karne ke liye (e.g. auto-
--    format, default value set karna) — data save hone se PEHLE intercept.
--    AFTER -> Audit logging, ya doosri tables update karna (jaise stock
--    reduce karna) — kyunki original operation CONFIRM ho chuka hota hai.
--
-- Q: Stored Function ke andar COMMIT/ROLLBACK kyun allowed nahi?
-- A: Function ko SELECT statement ke andar (read context) use kiya ja
--    sakta hai — agar function transaction control kare to ye SELECT ke
--    semantics ko unpredictable bana dega. MySQL isay explicitly disallow karta hai.
