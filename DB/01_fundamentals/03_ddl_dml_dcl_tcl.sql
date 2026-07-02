-- ============================================================================
--  SQL COMMAND CATEGORIES — DDL, DML, DCL, TCL, DQL
--  (Interview me "categorize this command" type questions aate hain)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- DDL — Data Definition Language (STRUCTURE define/change karta hai)
-- AUTO-COMMIT hota hai (rollback nahi ho sakta transaction me!)
-- ----------------------------------------------------------------------------
-- CREATE, ALTER, DROP, TRUNCATE, RENAME

USE company_db;

CREATE TABLE demo_ddl (id INT PRIMARY KEY, name VARCHAR(50));

ALTER TABLE demo_ddl ADD COLUMN email VARCHAR(100);
ALTER TABLE demo_ddl MODIFY COLUMN name VARCHAR(100);          -- type/size change
ALTER TABLE demo_ddl CHANGE COLUMN name full_name VARCHAR(100); -- rename + type change
ALTER TABLE demo_ddl DROP COLUMN email;
ALTER TABLE demo_ddl ADD INDEX idx_full_name (full_name);
ALTER TABLE demo_ddl RENAME TO demo_renamed;

-- TRUNCATE vs DELETE (CLASSIC INTERVIEW Q):
-- TRUNCATE TABLE demo_renamed;
-- - DDL hai, auto-commit, ROLLBACK nahi ho sakta
-- - WHERE clause allowed nahi (sari rows remove)
-- - AUTO_INCREMENT counter RESET ho jata hai
-- - Triggers FIRE nahi hote
-- - Bohat fast hai (table drop+recreate jaisa internally)
--
-- DELETE FROM demo_renamed;
-- - DML hai, transaction me ROLLBACK ho sakta hai
-- - WHERE clause allowed (selective delete)
-- - AUTO_INCREMENT counter reset NAHI hota
-- - Triggers FIRE hote hain
-- - Row-by-row delete hone ki wajah se TRUNCATE se SLOW hai bade table pe

DROP TABLE demo_renamed;

-- ----------------------------------------------------------------------------
-- DML — Data Manipulation Language (DATA change karta hai)
-- Transaction me ROLLBACK ho sakta hai
-- ----------------------------------------------------------------------------
-- INSERT, UPDATE, DELETE

INSERT INTO products (name, category, price, stock) VALUES ('Keyboard', 'Electronics', 4500, 100);

-- Multi-row insert (single statement = bohat fast, single round-trip):
INSERT INTO products (name, category, price, stock) VALUES
    ('Webcam', 'Electronics', 6000, 50),
    ('Desk Lamp', 'Furniture', 2500, 80);

-- UPSERT (insert ya update agar duplicate key mil jaye):
INSERT INTO products (id, name, category, price, stock) VALUES (1, 'Laptop Pro', 'Electronics', 130000, 20)
ON DUPLICATE KEY UPDATE price = VALUES(price), stock = VALUES(stock);

UPDATE products SET stock = stock - 1 WHERE id = 1;

DELETE FROM products WHERE name IN ('Webcam', 'Desk Lamp');

-- ----------------------------------------------------------------------------
-- DQL — Data Query Language (sirf READ)
-- ----------------------------------------------------------------------------
-- SELECT (Kabhi kabhi DML me hi count hota hai, lekin alag bhi bola jata hai)

SELECT name, price FROM products WHERE category = 'Electronics' ORDER BY price DESC LIMIT 5;

-- ----------------------------------------------------------------------------
-- DCL — Data Control Language (PERMISSIONS)
-- ----------------------------------------------------------------------------
-- GRANT, REVOKE

-- CREATE USER 'app_user'@'%' IDENTIFIED BY 'StrongPass123!';
-- GRANT SELECT, INSERT, UPDATE ON company_db.* TO 'app_user'@'%';
-- GRANT SELECT ON company_db.employees TO 'readonly_user'@'%';  -- single table
-- REVOKE INSERT ON company_db.* FROM 'app_user'@'%';
-- FLUSH PRIVILEGES;
-- SHOW GRANTS FOR 'app_user'@'%';

-- ----------------------------------------------------------------------------
-- TCL — Transaction Control Language
-- ----------------------------------------------------------------------------
-- START TRANSACTION / BEGIN, COMMIT, ROLLBACK, SAVEPOINT
-- (Deep dive: 09_transactions_locking/ folder)

START TRANSACTION;

    UPDATE products SET stock = stock - 5 WHERE id = 1;
    SAVEPOINT before_second_update;
    UPDATE products SET stock = stock - 1000 WHERE id = 2;  -- galti se zyada deduct

    -- Sirf is part ko undo karna hai:
    ROLLBACK TO SAVEPOINT before_second_update;

COMMIT;  -- ab sirf pehla UPDATE permanent hua

-- ============================================================================
-- QUICK REFERENCE TABLE — Interview me "categorize karo" sawal ke liye
-- ============================================================================
-- | Category | Commands                                  | Rollback possible? |
-- |----------|---------------------------------------------|----------------------|
-- | DDL      | CREATE, ALTER, DROP, TRUNCATE, RENAME        | NO (auto-commit)     |
-- | DML      | INSERT, UPDATE, DELETE                       | YES (in transaction) |
-- | DQL      | SELECT                                        | N/A (read-only)      |
-- | DCL      | GRANT, REVOKE                                 | NO                   |
-- | TCL      | COMMIT, ROLLBACK, SAVEPOINT, START TRANSACTION| -                    |
