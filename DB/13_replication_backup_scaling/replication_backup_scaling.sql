-- ============================================================================
--  REPLICATION, BACKUP & SCALING — DevOps/Senior round topic
--  (Mostly conceptual + commands, kyunki ye admin/infra-level cheezein hain)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1) REPLICATION kya hai aur kyun?
-- ----------------------------------------------------------------------------
-- Ek MASTER (Primary) database ka data ek ya zyada SLAVE (Replica) databases
-- pe automatically copy hota rehta hai.
--
-- Kyun zaruri hai:
-- - High Availability: Master crash ho to Slave ko promote kar sakte ho
-- - Read Scaling: Reads ko Slaves pe distribute karo, Writes Master pe
--   (90% apps READ-HEAVY hote hain — ye HUGE performance win deta hai)
-- - Backup: Slave pe backup lo, Master pe load nahi padta
-- - Analytics: Heavy reporting queries Slave pe chalao, Master fast rahe

-- ----------------------------------------------------------------------------
-- 2) Replication kaise kaam karti hai (Binary Log based — default mechanism)
-- ----------------------------------------------------------------------------
--
--   MASTER                                    SLAVE
--   ┌──────────────────┐                      ┌──────────────────────┐
--   │ 1. Write query     │                      │                        │
--   │ 2. Binary Log (binlog)│ ── network ──>    │ 3. Relay Log (copy)    │
--   │    me record hoti   │   (IO Thread)        │    me likha jata hai   │
--   └──────────────────┘                      │ 4. SQL Thread relay log│
--                                              │    replay karta hai    │
--                                              └──────────────────────┘
--
-- Steps:
-- 1. Master apni har change ko BINARY LOG (binlog) me likhta hai
-- 2. Slave ka "IO Thread" master se connect ho kar binlog events copy karta
--    hai apni RELAY LOG me
-- 3. Slave ka "SQL Thread" relay log ko READ karke wahi changes apne data pe APPLY karta hai
--
-- Replication ASYNCHRONOUS hoti hai by default -> thora SI delay (replication
-- lag) ho sakta hai Master aur Slave ke data me.

SHOW MASTER STATUS;     -- Master pe: current binlog position dekho
SHOW SLAVE STATUS\G     -- Slave pe: replication health check (Seconds_Behind_Master important hai)

-- ----------------------------------------------------------------------------
-- 3) Replication Types
-- ----------------------------------------------------------------------------
-- | Type             | Behaviour                                                       |
-- |--------------------|--------------------------------------------------------------------|
-- | Asynchronous (default) | Master commit ke baad WAIT nahi karta slave ke liye — fast but lag possible |
-- | Semi-Synchronous    | Master commit se PEHLE kam se kam 1 slave ka ACK wait karta hai (safer, thora slow) |
-- | Synchronous (Galera/Group Replication) | Saare nodes EK SAATH commit (strongest consistency, complex setup) |

-- ----------------------------------------------------------------------------
-- 4) REPLICATION LAG — Production me bohat real problem
-- ----------------------------------------------------------------------------
-- Agar tum WRITE karke TURANT Slave se READ karo (e.g. user signup ke baad
-- foran profile fetch), aur replication lag ho, to PURANA/STALE data mil
-- sakta hai (jab tak slave catch up na kar le).
--
-- Solutions:
-- - Critical read-after-write operations Master se hi karo
-- - Application me "read your own writes" ke liye sticky session use karo
-- - Lag monitor karo (Seconds_Behind_Master) aur alert lagao

-- ----------------------------------------------------------------------------
-- 5) BACKUP STRATEGIES
-- ----------------------------------------------------------------------------
-- a) Logical Backup — mysqldump
--    Pura SQL statements (CREATE + INSERT) ka file banta hai. Readable,
--    portable (cross-version), lekin LARGE databases pe SLOW (restore bhi slow)
-- b) Physical Backup — Percona XtraBackup / MySQL Enterprise Backup
--    Raw data files copy karta hai. FAST (especially large DBs), hot backup
--    (bina downtime ke) support karta hai.
-- c) Snapshot Backup — Cloud-level (AWS RDS snapshot, EBS snapshot)
--    Infrastructure-level instant snapshot.

-- Logical backup example commands (terminal pe, SQL nahi):
-- mysqldump -u root -p company_db > backup.sql
-- mysqldump -u root -p --single-transaction company_db > backup.sql   -- InnoDB ke liye CONSISTENT backup bina lock ke
-- mysql -u root -p company_db < backup.sql                              -- restore

-- ⚠️ --single-transaction IMPORTANT hai InnoDB tables ke liye: ye ek
-- REPEATABLE READ transaction shuru karta hai (MVCC snapshot use karta hai)
-- isliye backup ke dauran TABLE LOCK nahi lagta aur application normal
-- kaam karti rehti hai (consistent backup bhi milta hai).

-- ----------------------------------------------------------------------------
-- 6) Point-in-Time Recovery (PITR)
-- ----------------------------------------------------------------------------
-- Full backup (e.g. raat 12 baje) + Binary Logs (har change ka record) ko
-- combine karke kisi BHI SPECIFIC TIME pe data restore kar sakte ho
-- (e.g. "accidental DROP TABLE se 2 minute pehle ka state").
-- Process: Full backup restore karo, phir binlog ko us specific timestamp
-- tak REPLAY karo (mysqlbinlog tool se).

-- ----------------------------------------------------------------------------
-- 7) SCALING STRATEGIES
-- ----------------------------------------------------------------------------
-- a) VERTICAL SCALING (Scale Up): Bigger server (zyada CPU/RAM/SSD)
--    - Simple, lekin EK LIMIT hai (hardware ki max capacity)
--
-- b) READ REPLICAS (Horizontal Read Scaling): Master + multiple Slaves
--    - Writes Master pe, Reads Slaves pe distribute (load balancer/app logic)
--    - Sabse common pehla scaling step
--
-- c) SHARDING (Horizontal Write Scaling): Data ko MULTIPLE databases me SPLIT
--    karna (e.g. customer_id % 4 ke hisab se 4 alag databases)
--    - Complex hai: cross-shard JOINS mushkil, application logic complex ho jati hai
--    - Tab use karte hain jab single server WRITE capacity bhi exceed ho jaye
--
-- d) CACHING LAYER (Redis/Memcached): DB tak query jaane se PEHLE cache
--    check karo — DB load HUGELY kam ho jata hai (detail: Redis-Laravel-Guide.docx)
--
-- e) PARTITIONING (within single server): Bade table ko logically chhote
--    pieces me todna (RANGE, LIST, HASH partitioning) — DB ke andar hi,
--    sharding ki tarah multiple SERVERS nahi chahiye

CREATE TABLE orders_partitioned (
    id INT AUTO_INCREMENT,
    customer_id INT,
    created_at DATE,
    total_price DECIMAL(10,2),
    PRIMARY KEY (id, created_at)
)
PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2023 VALUES LESS THAN (2024),
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION pmax  VALUES LESS THAN MAXVALUE
);
-- Query jab created_at filter karti hai, MySQL sirf RELEVANT partition scan
-- karta hai ("Partition Pruning") — bohat fast for time-series/large historical data

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: Master-Slave replication me Slave WRITE accept kar sakta hai?
-- A: By default NAHI (read_only=1 set karte hain Slave pe taake accidental
--    writes na hon, jo replication conflict create kar sakti hain). Multi-
--    master / Group Replication setups me writes multiple nodes pe possible
--    hote hain, lekin wo complex hai.
--
-- Q: Replication lag hone par kya problem ho sakti hai?
-- A: Stale reads (purana data dikhna) agar app Slave se turant read kare
--    write ke baad. Solution: critical reads Master se karo, ya replication
--    lag monitor karke threshold cross hone pe alert/failover karo.
--
-- Q: Sharding kab consider karoge, replication/read-replicas kab kaafi hain?
-- A: Agar bottleneck READS hain -> Read Replicas kaafi hain (simple).
--    Agar bottleneck WRITES hain (single master ki write capacity exceed
--    ho rahi hai) -> tab Sharding consider karte hain (complex trade-off,
--    aakhri resort hai kyunki application complexity bohat badh jati hai).
--
-- Q: mysqldump vs Physical backup (XtraBackup) me kab kya choose karoge?
-- A: Chhoti/medium DB, cross-version portability chahiye -> mysqldump.
--    Bohat badi production DB (GBs/TBs), fast backup/restore aur minimal
--    downtime chahiye -> Physical backup (XtraBackup/Percona).
