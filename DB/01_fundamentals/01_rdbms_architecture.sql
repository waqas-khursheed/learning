-- ============================================================================
--  RDBMS & MySQL ARCHITECTURE
--  (6 years experience level — internals-focused, basics jaldi cover honge)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1) RDBMS kya hai?
-- ----------------------------------------------------------------------------
-- RDBMS = Relational Database Management System.
-- Data TABLES (rows + columns) me store hota hai, aur tables aapas me
-- RELATIONSHIPS (foreign keys) ke zariye linked hote hain.
--
-- MySQL, PostgreSQL, Oracle, SQL Server — sab RDBMS hain.
-- MongoDB, Redis — ye NoSQL hain (relational model follow nahi karte).

-- ----------------------------------------------------------------------------
-- 2) MySQL ka HIGH-LEVEL Architecture (Layers)
-- ----------------------------------------------------------------------------
--
--   ┌─────────────────────────────────────────────┐
--   │           CLIENT (App / Laravel / CLI)        │
--   └───────────────────┬───────────────────────────┘
--                        │  TCP/Socket (Connection)
--   ┌───────────────────▼───────────────────────────┐
--   │  CONNECTION LAYER                              │
--   │  - Authentication, Thread handling, SSL        │
--   └───────────────────┬───────────────────────────┘
--   ┌───────────────────▼───────────────────────────┐
--   │  SQL LAYER (Server Layer) — Engine-Independent  │
--   │  - Parser        (SQL syntax check)            │
--   │  - Optimizer     (best execution plan banata)  │
--   │  - Query Cache   (MySQL 8.0 me REMOVE ho gaya) │
--   │  - Execution Engine                            │
--   └───────────────────┬───────────────────────────┘
--   ┌───────────────────▼───────────────────────────┐
--   │  STORAGE ENGINE LAYER (Pluggable!)              │
--   │  - InnoDB (default, transactional)             │
--   │  - MyISAM (legacy, non-transactional)           │
--   │  - Memory, Archive, CSV, etc.                  │
--   └───────────────────┬───────────────────────────┘
--   ┌───────────────────▼───────────────────────────┐
--   │  DISK (Data files, Logs, Indexes)              │
--   └─────────────────────────────────────────────────┘
--
-- KEY INTERVIEW POINT: MySQL ka storage engine "pluggable" hai — yani
-- HAR TABLE ka apna alag engine ho sakta hai (rare use-case, but possible).
-- SQL Layer (parser/optimizer) sab engines ke liye COMMON hai.

-- ----------------------------------------------------------------------------
-- 3) Query Life Cycle (Jab tum SELECT chalate ho to kya hota hai?)
-- ----------------------------------------------------------------------------
-- 1. Connection Layer: Client connect hota hai (auth, thread assign)
-- 2. Parser: SQL syntax check karta hai, "Parse Tree" banata hai
-- 3. Preprocessor: Table/Column exist karte hain? Permissions check
-- 4. Optimizer: Multiple execution plans banata hai, SABSE SASTA (cost-based)
--    plan choose karta hai — yahi "Query Execution Plan" hai jo EXPLAIN dikhata hai
-- 5. Execution Engine: Storage Engine API ko call karta hai (row by row fetch)
-- 6. Storage Engine (InnoDB): Disk/Buffer Pool se actual data laata hai
-- 7. Result Set: Client ko wapis bheja jata hai
--
-- NOTE: MySQL 8.0 me "Query Cache" hata diya gaya hai (pehle versions me tha)
-- kyunki real-world me ye zyada problems create karta tha (har UPDATE pe
-- poora cache invalidate hota tha — high-write systems me slow ho jata tha).

-- ----------------------------------------------------------------------------
-- 4) Storage Engines Comparison (INTERVIEW FAVOURITE)
-- ----------------------------------------------------------------------------
-- | Feature             | InnoDB (default)         | MyISAM              |
-- |----------------------|---------------------------|----------------------|
-- | Transactions (ACID)  | YES                       | NO                   |
-- | Foreign Keys         | YES                       | NO                   |
-- | Row-level Locking    | YES                       | NO (table-level only)|
-- | Crash Recovery       | YES (redo/undo log)       | Weak                 |
-- | Full-text Search     | YES (5.6+)                | YES                  |
-- | MVCC (no read locks) | YES                       | NO                   |
-- | Storage              | Clustered Index (PK order)| Heap (insertion order)|
-- | Best for             | OLTP, concurrent writes   | Read-heavy, legacy   |
--
-- Real interview answer: "MySQL 5.5 ke baad se InnoDB default engine hai,
-- aur 99% production cases me InnoDB hi use karna chahiye kyunki transactions,
-- FK, row-locking sab chahiye hote hain."

-- Check current default engine:
SHOW VARIABLES LIKE 'default_storage_engine';

-- Check engine of a specific table:
SHOW TABLE STATUS LIKE 'employees';

-- Change engine of existing table (rarely needed):
-- ALTER TABLE employees ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- 5) MySQL Server Variables / Config (production me ye sab tune hote hain)
-- ----------------------------------------------------------------------------
SHOW VARIABLES LIKE 'innodb_buffer_pool_size';   -- RAM cache for data+index (sabse important tuning param)
SHOW VARIABLES LIKE 'max_connections';            -- kitne parallel connections allowed
SHOW VARIABLES LIKE 'innodb_log_file_size';       -- redo log size
SHOW VARIABLES LIKE 'slow_query_log';             -- slow queries log ho rahi hain ya nahi
SHOW VARIABLES LIKE 'long_query_time';            -- "slow" ki definition (seconds)

-- ----------------------------------------------------------------------------
-- 6) Process / Memory Architecture (InnoDB specific — deep dive 11_storage_engine_internals/ me)
-- ----------------------------------------------------------------------------
-- - Buffer Pool: RAM me data+index pages cache hote hain (disk I/O minimize)
-- - Redo Log: crash ke baad committed transactions recover karne ke liye
-- - Undo Log: rollback aur MVCC (purani row versions) ke liye
-- - Doublewrite Buffer: partial page write se corruption se bachata hai
-- - Adaptive Hash Index: InnoDB khud frequently-used B+Tree paths ko hash index bana deta hai

-- ============================================================================
-- QUICK INTERVIEW Q&A
-- ============================================================================
-- Q: MySQL aur MariaDB me kya farq hai?
-- A: MariaDB MySQL ka fork hai (2009, jab Oracle ne Sun/MySQL acquire kiya).
--    API/syntax mostly compatible hai lekin internals (storage engines jaise
--    Aria) alag ho sakte hain.
--
-- Q: Query Cache MySQL 8.0 me kyun hata diya gaya?
-- A: High-concurrency writes me cache invalidation overhead zyada tha,
--    benefit se zyada cost tha. Ab application-level caching (Redis) recommended hai.
--
-- Q: Default port kya hai?
-- A: 3306
