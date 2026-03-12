<?php 



// Normalization vs Denormalization – Overview
// | Concept              | Normalization                                                               | Denormalization                                                                |
// | -------------------- | --------------------------------------------------------------------------- | ------------------------------------------------------------------------------ |
// | **Meaning**          | Data ko **break karna** multiple related tables me, taake duplication na ho | Data ko **combine karna** (ya duplicate rakhna) taake queries fast chal sakein |
// | **Goal**             | Reduce redundancy (duplicate data kam karna)                                | Improve performance (joins kam karna)                                          |
// | **Best For**         | Write-heavy systems (like CRMs, ERPs)                                       | Read-heavy systems (like analytics, dashboards)                                |
// | **Joins**            | Zyada joins required                                                        | Kam joins required                                                             |
// | **Storage**          | Kam space lagta hai                                                         | Zyada space lagta hai                                                          |
// | **Data Consistency** | High (ek data ek jagah)                                                     | Low (same data multiple jagah)                                                 |

// 🔹 Example – Normalization
// 🎯 Scenario:

// Humare paas ek e-commerce system hai.
// Har Order me ek Customer hota hai.

// Agar hum Normalization use karein:

// 🧩 Table 1: customers

// | id | name | email                                   |
// | -- | ---- | --------------------------------------- |
// | 1  | Ali  | [ali@gmail.com](mailto:ali@gmail.com)   |
// | 2  | Sara | [sara@gmail.com](mailto:sara@gmail.com) |

// 🧩 Table 2: orders
// | id | customer_id | total | date       |
// | -- | ----------- | ----- | ---------- |
// | 1  | 1           | 5000  | 2025-11-13 |
// | 2  | 2           | 3000  | 2025-11-12 |


// Ab agar hume order ke customer ka naam chahiye,
// to hum query likhenge:

// SELECT orders.id, customers.name, orders.total
// FROM orders
// JOIN customers ON customers.id = orders.customer_id;

// Fayda:

// Customer ka data ek hi jagah store hai.

// Agar email change karein, to sirf customers table me update karna hai.

// ❌ Nuksaan:

// Query thodi complex ho gayi (join lagana padta hai).

// Large data me joins slow ho sakte hain.

// Example – Denormalization

// Ab hum performance ke liye denormalize karte hain:

//     | id | customer_id | customer_name | customer_email                          | total | date       |
// | -- | ----------- | ------------- | --------------------------------------- | ----- | ---------- |
// | 1  | 1           | Ali           | [ali@gmail.com](mailto:ali@gmail.com)   | 5000  | 2025-11-13 |
// | 2  | 2           | Sara          | [sara@gmail.com](mailto:sara@gmail.com) | 3000  | 2025-11-12 |


// Ab hume join nahi lagana padta:

// SELECT customer_name, total FROM orders;


// ✅ Fayda:

// Query fast (join nahi laga)

// Reporting ke liye ideal

// ❌ Nuksaan:

// Agar customer ka naam badla, to sari orders rows update karni padengi.

// Duplicate data zyada hai → space zyada lagta hai.

// | Situation                    | Use Normalization | Use Denormalization |
// | ---------------------------- | ----------------- | ------------------- |
// | Frequent data updates        | ✅                 | ❌                   |
// | Reporting / analytics        | ❌                 | ✅                   |
// | Small DB with simple queries | ✅                 | ❌                   |
// | Large scale with heavy reads | ❌                 | ✅                   |
// | Data consistency priority    | ✅                 | ❌                   |
// | Query performance priority   | ❌                 | ✅                   |


// Real-world example (Laravel point of view)

// Normalized:
// Models: User, Order, Product
// Use Eloquent relationships: $order->user->name

// Denormalized:
// Directly store user_name and user_email in orders table for faster report queries.

// 🔹 Architect’s Decision Logic

// Jab system design karte ho to architect ye sochta hai:

// “Kya mujhe consistency zyada chahiye ya performance?”

// Agar tumhara system:

// Transactional hai (like payments, orders, banking) → Normalization

// Analytics/reporting hai (like dashboards, BI) → Denormalization