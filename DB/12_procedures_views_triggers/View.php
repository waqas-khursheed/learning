<?php 



// View = ek saved SQL query jo database ke andar table ki tarah use hoti hai.

// Matlab, aap ek complex query ko save kar lo aur baad mein table ki tarah SELECT kar lo.

// Database object hota hai.

// Data store nahi karta, sirf query ka result show karta hai.

// Agar original table change ho → view ka data bhi update ho jata hai automatically.


// View Banane Ka Syntax
// CREATE VIEW view_name AS
// SELECT column1, column2
// FROM table_name
// WHERE condition;

// Example — Simple View

// Database me users table hai:

// CREATE TABLE users (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     name VARCHAR(100),
//     email VARCHAR(100),
//     status VARCHAR(20)
// );


// View: sirf active users ke liye:

// CREATE VIEW active_users AS
// SELECT id, name, email
// FROM users
// WHERE status = 'active';


// Use:

// SELECT * FROM active_users;


// Ye table ki tarah kaam karega, lekin actual data users table me hai.

// View Ke Types
// Type	Description
// Simple View	Ek table ya basic query ka result
// Complex View	Multiple tables ka JOIN, aggregate functions, GROUP BY
// Materialized View	Kuch databases me (PostgreSQL, Oracle) → data save hota hai, fast read ke liye
//  View Example — Multiple Tables

// orders table + users table:

// CREATE VIEW order_summary AS
// SELECT o.id AS order_id, u.name AS user_name, o.total_price, o.created_at
// FROM orders o
// JOIN users u ON o.user_id = u.id;


// Use:

// SELECT * FROM order_summary;



// Complex query save karna → baar-baar type nahi karna padta

// Security → user ko sirf view ka access do, original table ka nahi

// Easy Maintenance → query ko ek jagah update karna

// Readable Data → readable column names + joins ka simplified view

// View Me Limitations

// View me INSERT/UPDATE/DELETE har case me allowed nahi hota.

// Agar view me JOIN ya aggregate ho → update nahi hota.

// Views me parameters nahi hotay (stored procedure me hote hain).

// Large views → slow ho sakte hain (specially without indexes).

//  View Me UPDATE / INSERT / DELETE Ka Example

// Agar view simple hai (ek table ka data):

// CREATE VIEW active_users_view AS
// SELECT id, name, email FROM users WHERE status='active';


// SELECT * FROM active_users_view → works

// UPDATE active_users_view SET name='Ali Updated' WHERE id=1 → works

// Agar view me join hai → usually update nahi hota

// View Delete / Drop
// DROP VIEW IF EXISTS active_users;


// Stored Procedure vs View (Quick Comparison)

// | Feature          | Stored Procedure                  | View                          |
// | ---------------- | --------------------------------- | ----------------------------- |
// | Type             | Function / Logic                  | Saved Query                   |
// | Data Store       | No                                | No (except materialized view) |
// | Parameters       | Yes (IN/OUT)                      | No                            |
// | Multiple Queries | Yes                               | Usually 1 SELECT              |
// | Can Update Data  | Yes (INSERT/UPDATE/DELETE inside) | Only simple views             |
// | Execution        | CALL procedure                    | SELECT * FROM view            |


// Real-Life Example

// Database me:

// users table

// books table

// orders table

// View: User Orders Summary

// CREATE VIEW user_orders_summary AS
// SELECT u.id AS user_id, u.name AS user_name, b.title AS book_title, o.quantity, o.total_price
// FROM orders o
// JOIN users u ON o.user_id = u.id
// JOIN books b ON o.book_id = b.id;


// Use:

// SELECT * FROM user_orders_summary WHERE user_id=1;


// Ab aapko ek simple SELECT se user ka order summary mil jaye ga, har baar complex join likhne ki zarurat nahi.


// 1️ Stored Procedure (SP) ka Concept

// Stored Procedure = Database ke andar ek “function” hai jo SQL queries aur logic ko save karta hai.

// 🔹 Key Points:

// Database me object banta hai, table ki tarah.

// Multiple queries ek saath chala sakta hai: SELECT, INSERT, UPDATE, DELETE.

// Parameters le sakta hai: IN, OUT, INOUT.

// Call karna hota hai: CALL procedure_name();

// Data store nahi hota, sirf query execute hoti hai.

// 2️ Stored Procedure ke Benefits
//  1. Performance

// Procedure compile ho ke database me save ho jata hai → fast execution.

// Node.js ya Laravel se direct queries baar-baar run karne se fast hota hai.

//  2. Security

// User ko direct table access dene ki zarurat nahi.

// Sirf procedure call karne ka access do → database secure.

//  3. Reusability

// Ek hi procedure ko multiple places se call kar sakte ho.

// Backend me Node.js ya Laravel dono se use ho sakta hai.

//  4. Centralized Logic

// Complex logic database me rakh sakte ho → backend me code kam likhna padta hai.

// 3️ Stored Procedure ka Example (Story_Book Database)
// DELIMITER $$

// -- Get all books
// CREATE PROCEDURE get_all_books()
// BEGIN
//     SELECT id, title, author, price FROM books;
// END$$

// -- Add new book
// CREATE PROCEDURE add_book(IN p_title VARCHAR(150), IN p_author VARCHAR(100), IN p_price DECIMAL(10,2))
// BEGIN
//     INSERT INTO books (title, author, price) VALUES (p_title, p_author, p_price);
// END$$

// DELIMITER ;


// Node.js me call:

// const [rows] = await db.query("CALL get_all_books()");


// Laravel me call:

// $books = DB::select("CALL get_all_books()");

// 4️ Database me Stored Procedure kaam kaise karta hai

// Procedure database object ke roop me save hota hai → koi naya table nahi banta.

// Jab aap CALL procedure_name() karte ho:

// SQL engine procedure ke logic ko execute karta hai.

// Result aapko application me milta hai.

// Tables ka data procedure ke andar read / modify ho sakta hai, lekin procedure khud data store nahi karta.

// Note: Tables ke andar stored procedure apne queries ke liye reference karta hai, data wahan se fetch ya modify hota hai.

// 5️ View ka Concept

// View = ek saved SELECT query jo database me table ki tarah use hoti hai.

// 🔹 Key Points:

// Data store nahi hota (except materialized view).

// Multiple tables ka join ya filter simplify kar deta hai.

// Application me use simple SELECT * FROM view_name se hota hai.

// 6️ View ke Benefits
//  1. Complex Query Simplify

// Backend me baar-baar complex joins likhne ki zarurat nahi.

//  2. Security

// User ko sirf view ka access do → underlying table secure.

//  3. Reusability

// Ek query ko multiple places use kar sakte ho.

//  4. Real-Time Data

// View ka data hamesha latest table data ke saath update hota hai.

// 7️ View Example
// -- Active users view
// CREATE VIEW active_users AS
// SELECT id, name, email FROM users WHERE status = 'active';

// -- Order summary view
// CREATE VIEW order_summary AS
// SELECT o.id AS order_id, u.name AS user_name, b.title AS book_title, o.quantity, o.total_price
// FROM orders o
// JOIN users u ON o.user_id = u.id
// JOIN books b ON o.book_id = b.id;


// Node.js me call:

// const [rows] = await db.query("SELECT * FROM active_users");


// Laravel me call:

// $activeUsers = DB::table('active_users')->get();


// Procedure vs View (Node.js / Laravel Context)

// | Feature         | Stored Procedure         | View                              |
// | --------------- | ------------------------ | --------------------------------- |
// | Purpose         | Multiple queries + logic | Saved SELECT query                |
// | Parameters      | Yes (IN/OUT)             | No                                |
// | Execution       | CALL                     | SELECT                            |
// | Update Data     | Yes                      | Only simple views                 |
// | Application Use | Node.js / Laravel call   | Node.js / Laravel call like table |
// | Security        | High (logic inside DB)   | Moderate (hide table details)     |


// 9️ Database Perspective

// Procedure aur view table nahi banta.

// Ye metadata ke roop me database ke objects ban jate hain.

// MySQL / MariaDB / PostgreSQL me ye INFORMATION_SCHEMA.ROUTINES aur INFORMATION_SCHEMA.VIEWS me save hote hain.

// 10️ Best Practices

// Procedure → Complex logic, multiple queries, transactions

// View → Filtered data, joins, simplified SELECT

// Dono → Backend se call karke use karo → Node.js / Laravel dono compatible