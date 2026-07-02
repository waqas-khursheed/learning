-- ============================================================================
--  INTERVIEW Q&A — MASTER REVISION FILE (6 Years Experience Level)
--  Har topic file ke end me Q&A pehle se hai — ye file un sab ka SUMMARY +
--  SCENARIO-BASED questions hai jo specifically interviews me cross-topic
--  poochhe jate hain. Final revision se EK DIN PEHLE isay padho.
-- ============================================================================

-- ============================================================================
-- SECTION A: RAPID-FIRE CONCEPTUAL (warm-up round me poochhe jate hain)
-- ============================================================================

-- Q1: DROP, DELETE, TRUNCATE me farq?
-- A: DROP -> poori table/structure hi remove (DDL). TRUNCATE -> saari rows
--    remove, structure rehta hai, auto-commit, rollback nahi (DDL).
--    DELETE -> selective rows remove (WHERE), transaction me rollback ho
--    sakta hai (DML), triggers fire hote hain, slowest of the three.

-- Q2: Primary Key aur Unique Key me farq? [[detail: 02_keys_constraints]]
-- A: PK -> NULL allowed nahi, 1 per table, clustered index banata hai
--    (InnoDB). Unique -> NULL multiple baar allowed, multiple per table,
--    sirf secondary index.

-- Q3: CHAR vs VARCHAR? [[detail: 01_fundamentals/02_data_types]]
-- A: CHAR fixed-length (padded), VARCHAR variable-length (length-prefixed).
--    CHAR fast for fixed-size data, VARCHAR space-efficient for variable data.

-- Q4: INNER JOIN vs LEFT JOIN? [[detail: 03_joins]]
-- A: INNER -> sirf matching rows dono tables me. LEFT -> left table ki
--    SAARI rows + matching right (no match = NULL).

-- Q5: WHERE vs HAVING? [[detail: 05_aggregation_grouping]]
-- A: WHERE -> grouping se pehle row-level filter (aggregate fn allowed
--    nahi). HAVING -> grouping ke baad group-level filter (aggregate fn allowed).

-- Q6: Clustered vs Non-Clustered Index? [[detail: 07_indexes]]
-- A: Clustered -> leaf nodes me actual data (InnoDB: PK hamesha clustered).
--    Non-clustered (secondary) -> leaf nodes me PK value, extra lookup
--    chahiye actual row ke liye.

-- Q7: ACID properties? [[detail: 09_transactions_locking]]
-- A: Atomicity, Consistency, Isolation, Durability.

-- Q8: MySQL default isolation level? Default storage engine?
-- A: REPEATABLE READ. InnoDB (5.5+).

-- Q9: Normalization vs Denormalization? [[detail: 10_normalization]]
-- A: Normalization -> redundancy kam, data integrity zyada (writes ke liye
--    behtar). Denormalization -> redundancy jaan-bujh kar (reads ke liye
--    behtar, JOINs kam).

-- ============================================================================
-- SECTION B: SCENARIO-BASED (Senior/Lead level — "tumhe ye situation mile to kya karoge")
-- ============================================================================

-- ----------------------------------------------------------------------------
-- SCENARIO 1: "Hamari ek query production me 200ms se 8 second tak slow ho
-- gayi hai achanak — table size same hai, code change nahi hua. Kya karoge?"
-- ----------------------------------------------------------------------------
-- A: Step-by-step debug:
--    1. EXPLAIN/EXPLAIN ANALYZE laga kar dekho execution plan CHANGE to nahi
--       hua (optimizer ne different index/plan choose kiya ho sakta hai)
--    2. ANALYZE TABLE chalao — stale statistics ki wajah se optimizer galat
--       decision le sakta hai (especially bulk insert/delete ke baad)
--    3. SHOW PROCESSLIST -> koi LOCK CONTENTION ya LONG-RUNNING transaction
--       to nahi block kar raha
--    4. Recent data growth check karo (rows badh gayi, index ab kaam nahi
--       kar raha jaisa pehle karta tha — cardinality change)
--    5. Server resources check karo (CPU, disk I/O, buffer pool hit ratio
--       — kahin doosri heavy query resources eat to nahi kar rahi)
--    6. Slow query log se pattern dekho (kab se slow hai, consistently ya
--       intermittent)

-- ----------------------------------------------------------------------------
-- SCENARIO 2: "E-commerce site banani hai — Orders, Products, Inventory.
-- High concurrency me 2 users SAME LAST item buy karne ki koshish karein
-- to kya hoga? Kaise prevent karoge overselling?"
-- ----------------------------------------------------------------------------
-- A: PESSIMISTIC LOCKING pattern (SELECT FOR UPDATE) [[detail: 09_transactions_locking]]:
START TRANSACTION;
SELECT stock FROM products WHERE id = 1 FOR UPDATE;   -- row LOCK le lo
-- application check kare: stock > 0?
UPDATE products SET stock = stock - 1 WHERE id = 1;
COMMIT;
-- Dusra concurrent transaction is row pe WAIT karega jab tak pehla COMMIT
-- na ho jaye — overselling IMPOSSIBLE ho jata hai.
-- Alternative: Optimistic locking with version column (kam contention me behtar)
-- aur ek atomic conditional UPDATE bhi kaafi hai:
UPDATE products SET stock = stock - 1 WHERE id = 1 AND stock > 0;
-- Agar affected_rows = 0 -> stock khatam tha, order reject karo

-- ----------------------------------------------------------------------------
-- SCENARIO 3: "Reporting dashboard ke liye complex query 5 tables JOIN
-- karke chalti hai aur 10 second leti hai. Business chahta hai real-time
-- jaisa fast dashboard. Kya approach loge?"
-- ----------------------------------------------------------------------------
-- A: [[detail: 10_normalization, 13_replication_backup_scaling]]
--    1. Pehle EXPLAIN se confirm karo indexes sahi hain
--    2. Agar dashboard "near real-time" chal sakta hai (1-5 min stale data
--       acceptable) -> SUMMARY/AGGREGATE table banao (cron job/event se
--       periodically refresh) — query phir sirf 1 table read karegi
--    3. READ REPLICA pe ye heavy reporting query chalao (Master pe load na pade)
--    4. Application-level CACHING (Redis) — same query repeatedly chal rahi
--       hai to result cache kar lo TTL ke sath
--    5. Agar TRUE real-time chahiye aur data volume bohat bada hai, to
--       OLAP-specific solution consider karo (ClickHouse/data warehouse) —
--       MySQL OLTP ke liye design hua hai, heavy analytics ke liye nahi

-- ----------------------------------------------------------------------------
-- SCENARIO 4: "Migration karni hai — bade production table (50 million
-- rows) me ek NOT NULL column add karna hai. Kaise karoge bina downtime ke?"
-- ----------------------------------------------------------------------------
-- A: MySQL 8.0 me INSTANT/ONLINE DDL support hai for many ALTER operations:
ALTER TABLE employees ADD COLUMN phone VARCHAR(20) NULL, ALGORITHM=INSTANT;
-- ALGORITHM=INSTANT (8.0.12+): metadata-only change, INSTANT (no table rewrite)
-- ALGORITHM=INPLACE: table rewrite hoti hai but ONLINE (reads/writes allowed)
-- ALGORITHM=COPY: OLD approach, poori table copy hoti hai, table LOCKED — AVOID
--
-- NOT NULL column add karne ka safe pattern (zero downtime):
-- 1. Column ko NULLABLE add karo (with DEFAULT) — fast, instant
-- 2. Background job se purani rows BATCH me UPDATE karo (chhote batches,
--    taake long transaction na bane aur replication lag na ho)
-- 3. Jab saari rows update ho jayein, phir ALTER TABLE MODIFY ... NOT NULL karo
-- Tools: pt-online-schema-change / gh-ost (Percona/GitHub tools) bade
-- production migrations ke liye industry-standard hain.

-- ----------------------------------------------------------------------------
-- SCENARIO 5: "Database design karo for a 'Library Management System' —
-- Books, Members, Borrowing." (Schema design round)
-- ----------------------------------------------------------------------------
-- A: Sample answer structure (explain TABLES + RELATIONSHIPS + WHY):
-- books (id, title, author, isbn UNIQUE, total_copies, available_copies)
-- members (id, name, email UNIQUE, membership_date)
-- borrowings (id, book_id FK, member_id FK, borrowed_at, due_at, returned_at NULL)
--   - returned_at NULL = currently borrowed, abhi return nahi hui (state ka indicator)
--   - available_copies DENORMALIZED hai (performance ke liye) — trigger se
--     maintain hoga (borrow pe -1, return pe +1)
-- Discuss: many-to-many (book <-> member via borrowings), indexes on FKs,
-- CHECK constraint (available_copies >= 0), composite index (member_id, returned_at)
-- for "current borrowed books of a member" query.

-- ============================================================================
-- SECTION C: COMPARISON TABLES — QUICK REVISION (sab ek jagah)
-- ============================================================================

-- | Topic                  | Quick Answer Pattern                                              |
-- |---------------------------|------------------------------------------------------------------------|
-- | DELETE vs TRUNCATE vs DROP| DML+rollback vs DDL+reset vs DDL+structure-gone                      |
-- | PK vs UNIQUE              | No NULL+1 per table vs NULL ok+multiple                               |
-- | CHAR vs VARCHAR            | Fixed+fast vs Variable+space-efficient                                |
-- | INNER vs LEFT JOIN          | Match-only vs Left-all+match                                          |
-- | WHERE vs HAVING              | Pre-group row filter vs Post-group filter                            |
-- | Clustered vs Secondary Index | Data-in-leaf vs PK-in-leaf+extra-lookup                              |
-- | Function vs Procedure         | Returns 1 value+SELECT-usable vs 0..N results+CALL-only            |
-- | Redo Log vs Undo Log           | Crash recovery (replay) vs Rollback+MVCC (old values)               |
-- | Optimistic vs Pessimistic Lock  | Version-check-on-write vs Lock-before-read (FOR UPDATE)            |
-- | Normalization vs Denormalization | Write-integrity-focused vs Read-performance-focused                |
-- | Vertical vs Horizontal Scaling    | Bigger server vs More servers (replicas/shards)                   |
-- | UNION vs UNION ALL                  | Dedupe (slower) vs No dedupe (faster)                            |

-- ============================================================================
-- SECTION D: "Explain karo jaise mujhe kuch nahi pata" — Internals me se ek
-- random poochha jata hai senior rounds me. In sab ko 2-3 minute me explain
-- karna practice karo (loudly bolkar, mirror ke saamne agar zarurat ho):
-- ============================================================================
-- 1. Jab tum SELECT chalate ho, MySQL internally kya steps follow karta hai?
--    [[01_fundamentals/01_rdbms_architecture]]
-- 2. Index B+Tree internally kaise organized hota hai aur kyun?
--    [[07_indexes/indexes_internals]]
-- 3. COMMIT karne par data physically kahan jata hai (redo log vs data page)?
--    [[11_storage_engine_internals/innodb_internals]]
-- 4. MVCC kaise readers/writers ko block hone se bachata hai?
--    [[09_transactions_locking/transactions_locking_mvcc]]
-- 5. EXPLAIN output dekh kar query ko optimize kaise karoge step by step?
--    [[08_query_optimization/query_optimization_explain]]
