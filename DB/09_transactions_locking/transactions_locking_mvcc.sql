-- ============================================================================
--  TRANSACTIONS, LOCKING & MVCC — Senior/Lead level topic
-- ============================================================================
USE company_db;

-- ----------------------------------------------------------------------------
-- 1) ACID Properties (THEORY — har interview me poocha jata hai)
-- ----------------------------------------------------------------------------
-- A — Atomicity:   Transaction ke saare statements ya to SAB hon ya KOI nahi
--                  (all-or-nothing). Beech me fail = poora ROLLBACK.
-- C — Consistency: Transaction ke pehle aur baad me database EK VALID state
--                  me hi rehta hai (constraints, FK, triggers violate nahi hote)
-- I — Isolation:   Concurrent transactions ek dusre ko (controlled tareeqe
--                  se) affect nahi karte — isolation LEVEL decide karta hai kitna
-- D — Durability:  COMMIT hone ke baad data PERMANENT hai (crash ke baad bhi
--                  safe — redo log ki wajah se)

-- ----------------------------------------------------------------------------
-- 2) Basic Transaction Example — Bank Transfer (CLASSIC use-case)
-- ----------------------------------------------------------------------------
START TRANSACTION;

    UPDATE employees SET salary = salary - 10000 WHERE id = 1;  -- deduct
    UPDATE employees SET salary = salary + 10000 WHERE id = 3;  -- add

    -- Agar dono successful -> COMMIT, agar koi error -> ROLLBACK
    -- (real app me: try/catch ke andar, exception pe ROLLBACK)

COMMIT;
-- Agar yahan COMMIT na karte aur connection drop ho jati, MySQL AUTOMATICALLY
-- ROLLBACK kar deta — Atomicity guarantee.

-- ----------------------------------------------------------------------------
-- 3) ISOLATION LEVELS (CLASSIC INTERVIEW — sabse zyada poocha jata hai is topic me)
-- ----------------------------------------------------------------------------
-- | Level             | Dirty Read | Non-Repeatable Read | Phantom Read | MySQL Default? |
-- |---------------------|--------------|------------------------|-----------------|-------------------|
-- | READ UNCOMMITTED     | YES          | YES                    | YES             | No               |
-- | READ COMMITTED       | NO           | YES                    | YES             | No (Oracle/PG default) |
-- | REPEATABLE READ      | NO           | NO                     | NO* (InnoDB me gap locks ki wajah se mostly prevented) | YES (MySQL default!) |
-- | SERIALIZABLE         | NO           | NO                     | NO              | No (sabse strict, sabse slow) |
--
-- Definitions:
-- - Dirty Read: Doosri transaction ka UNCOMMITTED data parh lena (jo rollback ho sakta hai)
-- - Non-Repeatable Read: Same transaction me same row do baar parho, value
--   DIFFERENT mile (kyunki beech me doosri transaction ne UPDATE kar diya aur COMMIT ho gaya)
-- - Phantom Read: Same transaction me same WHERE condition do baar chalao,
--   DIFFERENT number of rows milein (kyunki beech me doosri transaction ne INSERT kiya)

SELECT @@transaction_isolation;   -- current session ka isolation level dekho
SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;   -- change karne ka tareeqa

-- ----------------------------------------------------------------------------
-- 4) MVCC — Multi-Version Concurrency Control (InnoDB ka core magic)
-- ----------------------------------------------------------------------------
-- MVCC ki wajah se READERS aur WRITERS EK DOOSRE KO BLOCK NAHI karte!
-- (Traditional locking systems me read bhi lock leta tha — InnoDB me nahi)
--
-- Kaise kaam karta hai:
-- - Har row ke 2 hidden columns hote hain: DB_TRX_ID (kisne last modify kiya)
--   aur DB_ROLL_PTR (UNDO LOG ka pointer — purani version tak)
-- - Jab tum UPDATE karte ho, InnoDB OLD VALUE ko UNDO LOG me copy kar deta hai
--   aur row ko naye values se update kar deta hai (in-place)
-- - Doosri transaction agar abhi bhi PURANI value parhna chahti hai
--   (apne transaction snapshot ke hisab se), to UNDO LOG se purani version
--   reconstruct kar leti hai
-- - Isi wajah se SELECT (read) kabhi WRITE LOCK ka wait nahi karta
--   (REPEATABLE READ / READ COMMITTED me) — ye "Snapshot Read" kehlata hai
--
-- REPEATABLE READ me: Transaction shuru hote hi ek SNAPSHOT le leta hai,
-- poori transaction me WAHI snapshot dikhta hai (chahe doosri transactions
-- COMMIT karti rahein) — isi wajah se Non-Repeatable Read nahi hota.

-- ----------------------------------------------------------------------------
-- 5) LOCKING TYPES (InnoDB)
-- ----------------------------------------------------------------------------
-- a) Shared Lock (S) / Read Lock — multiple transactions same row par S
--    lock le sakti hain (parallel read allowed), lekin koi WRITE nahi kar sakta
SELECT * FROM employees WHERE id = 1 LOCK IN SHARE MODE;   -- (MySQL 8.0+: FOR SHARE)
-- ya:
SELECT * FROM employees WHERE id = 1 FOR SHARE;

-- b) Exclusive Lock (X) / Write Lock — sirf EK transaction le sakti hai,
--    baqi sab (read+write) wait karte hain
SELECT * FROM employees WHERE id = 1 FOR UPDATE;
-- "Pessimistic Locking" — row ko explicitly lock karke modify karne se pehle

-- c) Row-level Lock vs Table-level Lock
--    InnoDB DEFAULT = Row-level locking (sirf jo rows touch hui wo lock hoti hain)
--    MyISAM = Table-level locking (poori table lock, bohat zyada contention)
--    Yehi InnoDB ka biggest advantage hai high-concurrency systems me

-- d) Gap Lock & Next-Key Lock (REPEATABLE READ me Phantom Read rokne ke liye)
--    Gap Lock: index records ke BEECH ke "gap" ko lock karta hai (naya INSERT
--    rokne ke liye), even agar wahan koi row exist nahi karti
--    Next-Key Lock = Row Lock + Gap Lock (dono combine)

-- ----------------------------------------------------------------------------
-- 6) DEADLOCK — Jab do transactions ek dusre ka resource wait karein (circular wait)
-- ----------------------------------------------------------------------------
-- Transaction A: locks row 1, phir row 2 chahiye (jo B ne lock ki hui hai)
-- Transaction B: locks row 2, phir row 1 chahiye (jo A ne lock ki hui hai)
-- -> DEADLOCK! MySQL automatically detect karta hai aur EK transaction ko
--    KILL kar deta hai (ROLLBACK), error: "Deadlock found; try restarting transaction"
--
-- PREVENTION:
-- - Tables/rows ko HAMESHA SAME ORDER me access karo (sab transactions me consistent)
-- - Transactions ko CHHOTA aur FAST rakho (jitni jaldi commit utna kam risk)
-- - Application level pe retry logic lagao (deadlock error pe automatically retry)

SHOW ENGINE INNODB STATUS;   -- "LATEST DETECTED DEADLOCK" section me detail milta hai

-- ----------------------------------------------------------------------------
-- 7) Optimistic vs Pessimistic Locking (Application-level pattern)
-- ----------------------------------------------------------------------------
-- Pessimistic: "FOR UPDATE" se row ko PEHLE hi lock kar lo (high contention me behtar)
-- Optimistic: version/timestamp column rakho, UPDATE ke waqt check karo:
UPDATE products SET stock = stock - 1, version = version + 1
WHERE id = 1 AND version = 5;   -- agar version match nahi hua (0 rows affected) -> conflict detect, retry karo
-- Low contention systems me behtar hai (lock ka overhead nahi)

-- ============================================================================
-- INTERVIEW Q&A
-- ============================================================================
-- Q: MySQL ka DEFAULT isolation level kya hai, aur kyun?
-- A: REPEATABLE READ. (Most databases READ COMMITTED default rakhte hain,
--    lekin MySQL/InnoDB REPEATABLE READ ko default rakhta hai kyunki MVCC +
--    gap locks ki wajah se Phantom Reads bhi mostly prevent ho jate hain
--    bina performance zyada compromise kiye.)
--
-- Q: MVCC ka fayda kya hai?
-- A: Readers aur Writers ek dusre ko block nahi karte (high concurrency),
--    kyunki reads UNDO LOG se purani consistent snapshot parh lete hain
--    instead of waiting for write lock release.
--
-- Q: Deadlock aur normal Lock Wait me farq?
-- A: Lock Wait: transaction wait karti hai (eventually lock mil jayega jab
--    doosri transaction COMMIT/ROLLBACK kare). Deadlock: CIRCULAR wait hai
--    (kabhi resolve nahi hoga apne aap) — MySQL ek transaction ko forcibly
--    KILL karta hai isay resolve karne ke liye.
--
-- Q: SELECT FOR UPDATE kab use karoge?
-- A: Jab row PEHLE read karke phir UPDATE karni ho aur beech me koi aur
--    transaction usay modify na kar sake (e.g. inventory stock check-then-
--    deduct, seat booking systems) — "Pessimistic Locking" pattern.
