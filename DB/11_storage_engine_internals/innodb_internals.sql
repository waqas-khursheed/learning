-- ============================================================================
--  INNODB STORAGE ENGINE INTERNALS — "DB Engine kaise kaam karta hai"
--  Ye file purely CONCEPTUAL hai (theory + diagrams) — yahi cheez tumne
--  specifically pucha tha ke "db kaisy kaam krti hai is ka logic"
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1) InnoDB Memory + Disk Architecture (Big Picture)
-- ----------------------------------------------------------------------------
--
--   ┌─────────────────────── MEMORY (RAM) ────────────────────────────┐
--   │                                                                    │
--   │   ┌──────────────────────────────────────────────────────────┐   │
--   │   │  BUFFER POOL (sabse important — innodb_buffer_pool_size)   │   │
--   │   │  - Data pages (table rows) cache                            │   │
--   │   │  - Index pages (B+Tree nodes) cache                         │   │
--   │   │  - Adaptive Hash Index                                       │   │
--   │   │  - Change Buffer (secondary index changes batch karta hai)  │   │
--   │   └──────────────────────────────────────────────────────────┘   │
--   │                                                                    │
--   │   ┌─────────────────────┐   ┌─────────────────────┐               │
--   │   │  LOG BUFFER          │   │  DOUBLEWRITE BUFFER  │               │
--   │   │  (redo log RAM cache)│   │  (partial-write safety)│           │
--   │   └─────────────────────┘   └─────────────────────┘               │
--   └────────────────────────────────────────────────────────────────────┘
--                              │  (flush periodically)
--   ┌─────────────────────────▼──────────────────────────────────────┐
--   │                          DISK                                     │
--   │  - Tablespace files (.ibd) -> actual table data + indexes (B+Tree)│
--   │  - Redo Log files (ib_logfile*) -> crash recovery                 │
--   │  - Undo Log (inside system tablespace / undo tablespaces)         │
--   │  - Binary Log (binlog) -> replication + point-in-time recovery    │
--   └─────────────────────────────────────────────────────────────────┘

-- ----------------------------------------------------------------------------
-- 2) BUFFER POOL — Sabse important performance component
-- ----------------------------------------------------------------------------
-- - RAM me data aur index pages CACHE karta hai (disk I/O minimize karne ke liye)
-- - Pages 16KB chunks me load/manage hote hain (default innodb_page_size)
-- - LRU (Least Recently Used) algorithm se manage hota hai — kam use hone
--   wale pages evict hote hain jab buffer pool full ho jaye
-- - RULE OF THUMB: Production server RAM ka 60-70% buffer pool ko do
--   (taake "working set" — frequently accessed data — pura RAM me fit ho jaye)

SHOW VARIABLES LIKE 'innodb_buffer_pool_size';
SHOW STATUS LIKE 'Innodb_buffer_pool_read%';
-- Innodb_buffer_pool_reads (disk se padhna pada) vs
-- Innodb_buffer_pool_read_requests (total requests) — ratio se HIT RATE
-- nikalte hain. Cache Hit Ratio LOW hai to buffer pool size badhao.

-- ----------------------------------------------------------------------------
-- 3) WRITE PATH — Jab tum UPDATE/INSERT karte ho, INTERNALLY kya hota hai?
-- ----------------------------------------------------------------------------
-- Step-by-step (ye exact sequence interview me deeply pucha jata hai):
--
-- 1. Row Buffer Pool me LOAD hoti hai (agar already nahi hai to disk se padhi jati hai)
-- 2. UNDO LOG me OLD VALUE likhi jati hai (rollback + MVCC ke liye)
-- 3. Row Buffer Pool me MODIFY hoti hai (IN-MEMORY change, abhi disk pe nahi)
-- 4. REDO LOG (Log Buffer me, phir disk pe) me change record hota hai
--    -> "Write-Ahead Logging (WAL)" principle: pehle LOG likho, baad me data
-- 5. COMMIT hone par: redo log GUARANTEED disk pe flush hota hai (durability)
--    (innodb_flush_log_at_trx_commit=1 default — har commit pe fsync)
-- 6. Actual DATA PAGE (dirty page) ASYNC me baad me disk pe flush hoti hai
--    (background thread, "Checkpoint" mechanism se) — ye turant nahi hota!
--
-- KEY INSIGHT: COMMIT ka matlab hai "REDO LOG disk pe safe hai", NA ke
-- "actual data page disk pe likhi ja chuki hai". Crash recovery ke waqt
-- InnoDB redo log se SAARI committed changes REPLAY karta hai.

SHOW VARIABLES LIKE 'innodb_flush_log_at_trx_commit';
-- 1 = sabse safe (har commit pe disk fsync) — DEFAULT, ACID compliant
-- 0/2 = thora fast lekin crash pe data loss ka risk (kam durability)

-- ----------------------------------------------------------------------------
-- 4) REDO LOG vs UNDO LOG (CLASSIC CONFUSION — interview clear karo)
-- ----------------------------------------------------------------------------
-- | Aspect      | REDO LOG                              | UNDO LOG                       |
-- |---------------|------------------------------------------|------------------------------------|
-- | Purpose       | CRASH RECOVERY (committed changes REDO karna) | ROLLBACK + MVCC (purani values dikhana) |
-- | Kya store hota| Physical changes (kya badla)             | Logical old values (pehle kya tha) |
-- | Kab use hota  | Server crash ke baad startup pe           | ROLLBACK statement, ya MVCC snapshot read |
-- | Circular?     | YES (fixed size, purane overwrite hote hain after checkpoint) | Purge thread clean karta hai jab purani version ki zarurat na rahe |

-- ----------------------------------------------------------------------------
-- 5) CHECKPOINT — Dirty pages ko disk pe flush karne ka mechanism
-- ----------------------------------------------------------------------------
-- "Dirty Page" = wo page jo Buffer Pool me modify hui hai but disk pe abhi
-- purani value hai. Checkpoint process periodically dirty pages ko disk pe
-- likhta hai taake:
-- a) Redo log zyada bada na ho jaye (purane redo log entries discard ho sakein)
-- b) Crash recovery time kam rahe (kam changes replay karne padein)

-- ----------------------------------------------------------------------------
-- 6) DOUBLEWRITE BUFFER — Partial Page Write Problem se bachata hai
-- ----------------------------------------------------------------------------
-- Disk page size (16KB) aur OS/filesystem write size (4KB) match nahi karte
-- -> crash agar BEECH me ho (partial write) to page CORRUPT ho sakta hai.
-- InnoDB pehle page ko ek "doublewrite buffer" area me likhta hai (sequential,
-- safe), phir actual location pe. Crash hone par doublewrite buffer se
-- valid copy mil jati hai recovery ke liye.

-- ----------------------------------------------------------------------------
-- 7) CHANGE BUFFER — Secondary Index INSERT/UPDATE ko optimize karta hai
-- ----------------------------------------------------------------------------
-- Agar secondary index ka page Buffer Pool me LOADED nahi hai, InnoDB turant
-- disk se page load nahi karta — change ko "Change Buffer" me temporarily
-- rakh leta hai aur baad me (jab page anyway access ho) MERGE kar deta hai.
-- Isse RANDOM I/O kam hoti hai, especially write-heavy workloads me.

-- ----------------------------------------------------------------------------
-- 8) BINARY LOG (binlog) — Replication aur Point-in-Time Recovery ke liye
-- ----------------------------------------------------------------------------
-- - Server-level log (InnoDB-specific nahi), har DATA-CHANGING statement
--   (ya row change, mode pe depend karta hai) record karta hai
-- - Replication ka FOUNDATION hai (master apna binlog slave ko bhejta hai)
-- - 3 formats: STATEMENT (actual SQL), ROW (row-level changes), MIXED (dono)
SHOW VARIABLES LIKE 'log_bin';
SHOW BINARY LOGS;

-- ----------------------------------------------------------------------------
-- 9) Table data PHYSICALLY kaise stored hai (.ibd files)
-- ----------------------------------------------------------------------------
SHOW VARIABLES LIKE 'innodb_file_per_table';
-- ON (default) -> har table ka apna alag .ibd file hota hai (data/datadir/dbname/tablename.ibd)
-- OFF -> sab tables ek shared system tablespace (ibdata1) me

-- ----------------------------------------------------------------------------
-- 10) Read Path Summary (jab SELECT chalate ho)
-- ----------------------------------------------------------------------------
-- 1. Optimizer best execution plan banata hai (kaunsi index, kaunsa join algo)
-- 2. Storage Engine API call hoti hai row-by-row fetch ke liye
-- 3. InnoDB pehle BUFFER POOL me dekhta hai (page already cached hai?)
-- 4. Agar miss ho (not in buffer pool) -> DISK se page (16KB) read karta hai
--    aur Buffer Pool me cache kar leta hai (future reads fast hon)
-- 5. MVCC snapshot ke hisab se correct row VERSION return hoti hai
--    (current ya undo log se reconstructed purani version)

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: "COMMIT" karne ke baad data turant disk pe likh diya jata hai?
-- A: REDO LOG turant (guaranteed) disk pe flush hota hai — yehi durability
--    guarantee deta hai. Actual DATA PAGE (table file) ASYNC me baad me
--    flush hoti hai (checkpoint mechanism se). Crash hone par redo log se
--    committed changes REPLAY ho jate hain.
--
-- Q: Write-Ahead Logging (WAL) kya hai aur kyun zaruri hai?
-- A: Principle: koi bhi data change DISK pe likhne se PEHLE, uska LOG
--    (redo log) disk pe likha jata hai. Isse crash recovery possible hoti
--    hai — agar crash ho jaye to log replay karke data consistent state
--    me wapis le aate hain, bina actual data pages turant flush kiye.
--
-- Q: innodb_buffer_pool_size kitna rakhna chahiye production me?
-- A: Dedicated DB server ka 60-70% RAM. Agar buffer pool me poora "working
--    set" (frequently accessed data+index) fit ho jaye, disk I/O bohat kam
--    ho jata hai aur performance dramatically improve hoti hai.
--
-- Q: Redo Log full ho jaye to kya hoga?
-- A: Naye writes BLOCK ho jate hain jab tak checkpoint process dirty pages
--    flush na kare aur space free na ho — isliye innodb_log_file_size ko
--    sahi size karna important hai (zyada bada = crash recovery slow,
--    zyada chhota = checkpoint zyada frequent, write throughput kam).
