# MySQL Mastery Syllabus — Basic se Advanced (6 Years Dev ke liye)

> **Maqsad:** Tumhare `Learning_Roadmap_Time_Plan.md` me Database ko
> "🔴 SABSE KAMZOR AREA" mark kiya gaya tha (sirf 2 files). Ye poora `DB/`
> folder us gap ko fill karta hai — **basics → internals → optimization →
> interview-tricky-queries**, sab ek hi consistent schema (`company_db`) pe
> real examples ke saath.

> **Pehla kaam:** [`00_schema.sql`](00_schema.sql) ko apne MySQL me run karo.
> Yehi schema (departments, employees, customers, products, orders) HAR
> file me reuse hota hai — taake examples copy-paste karke turant chala sako.

---

## Kaise Padhna Hai (Order Important Hai)

Tumhara level "basic CRUD" nahi hai — isliye har file me theory thori hai,
**examples aur "kyun/internally kaise kaam karta hai" zyada hai.** Interview
ke liye sabse zyada poochhe jane wale topics ko 🎯 mark kiya hai.

| # | Folder | Topic | Kyun Important | Interview Weight |
|---|--------|-------|-----------------|-------------------|
| 1 | `01_fundamentals/` | Architecture, Data Types, DDL/DML/DCL/TCL | Foundation — galat data type = future me performance issue | ⭐⭐ |
| 2 | `02_keys_constraints/` | PK, FK, Unique, Composite Keys | Data integrity ka base | ⭐⭐⭐ |
| 3 | `03_joins/` | INNER, LEFT, RIGHT, FULL, SELF, CROSS | Roz ka kaam | 🎯🎯🎯 |
| 4 | `04_subqueries_unions/` | Subquery, CTE, UNION vs UNION ALL | Complex reports likhne ke liye | 🎯🎯 |
| 5 | `05_aggregation_grouping/` | GROUP BY, HAVING, Aggregate fns | Dashboard/reports ka backbone | 🎯🎯🎯 |
| 6 | `06_window_functions/` | ROW_NUMBER, RANK, LEAD/LAG, Running totals | Senior-level differentiator | 🎯🎯🎯 |
| 7 | `07_indexes/` | B+Tree internals, Clustered vs Secondary, Composite index | **Sabse zyada poochha jane wala deep topic** | 🎯🎯🎯🎯 |
| 8 | `08_query_optimization/` | EXPLAIN, Optimizer, Slow query, N+1 | Production performance | 🎯🎯🎯🎯 |
| 9 | `09_transactions_locking/` | ACID, Isolation levels, MVCC, Deadlocks | Senior/Lead level question | 🎯🎯🎯🎯 |
| 10 | `10_normalization/` | 1NF–5NF, Denormalization tradeoffs | Schema design rounds | 🎯🎯 |
| 11 | `11_storage_engine_internals/` | InnoDB internals, Buffer Pool, Redo/Undo log | "DB engine kaise kaam karta hai" — exactly jo tumne pucha | 🎯🎯🎯 |
| 12 | `12_procedures_views_triggers/` | Stored Procedure, View, Trigger, Function | (View.php, Stored_Procedure.php already maujood hain) | ⭐⭐ |
| 13 | `13_replication_backup_scaling/` | Master-slave, Backup, Sharding, Read replicas | DevOps/Senior round | ⭐⭐⭐ |
| 14 | `14_tricky_queries/` | Nth salary, duplicates, gaps & islands, recursive CTE | **Live coding round ka core** | 🎯🎯🎯🎯🎯 |
| 15 | `15_interview_questions/` | Curated Q&A, scenario-based | Final revision se pehle | 🎯🎯🎯🎯 |

---

## Suggested Time Plan (Tumhare existing roadmap ke hisab se — Database 2 mahine slot)

**Week 1–2:** Fundamentals → Keys → Joins → Subqueries/Unions
(roz 1 ghanta, har query khud chala kar dekho, `EXPLAIN` lagana shuru kar do)

**Week 3–4:** Aggregation → Window Functions → Indexes (deep)
(Indexes wali file 2 baar padho — interview me sabse zyada is pe ghera jata hai)

**Week 5–6:** Query Optimization → Transactions/Locking → Normalization
(EXPLAIN ANALYZE har query pe chalana practice karo)

**Week 7:** Storage Engine Internals → Replication/Backup/Scaling
(Ye "system design" angle hai — senior interviews me yahan se sawal aata hai)

**Week 8:** Procedures/Views/Triggers → Tricky Queries → Interview Q&A
(Tricky queries file ko bila dekhe likhne ki practice karo — yehi asli test hai)

---

## Golden Rule

> Sirf padhna kaafi nahi — har file ka har query `company_db` pe khud chalao,
> `EXPLAIN` lagao, result dekho, phir query ko thora modify karke dobara try
> karo. Jab tak query khud se likh na sako, samjho topic abhi pakka nahi hua.
