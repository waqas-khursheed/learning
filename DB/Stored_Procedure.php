<?php 


//  Stored Procedure Kya Hoti Hai?

// Stored Procedure aik database ke andar saved function hoti hai.
// Matlab aap SQL queries ko ek jagah save kar detay ho, phir jab chaho usko call kr k chala dete ho.

// Bilkul aik function ki tarah hoti hai.

//  Stored Procedure Kyu Banai Jati Hai? (Benefits)
// ✔️ 1. Repeated Queries ko Save Karna

// Agar aik hi query bar-bar chalani ho → procedure bana lo.

// ✔️ 2. Fast Performance

// Procedure database ke andar compile ho jati hai.
// Next time run karo → 10x fast.

// ✔️ 3. Security

// Aap user ko direct table access na do →
// sirf procedure chalane ka access do.
// Isse data secure rehta hai.

// ✔️ 4. Complex Logic Database ke andar

// Loops
// IF-ELSE
// Calculations
// Jo application mein hoti hain wo database mein chal jaati hain.

// ✔️ 5. Multiple Queries ek hi Procedure mein

// Aap ek hi procedure mein:

// SELECT
// INSERT
// UPDATE
// DELETE
// IF
// LOOP
// sab chala sakte ho.

// Procedure Kis Tarah Banti Hai?
// 🔹 MySQL Example

// DELIMITER $$

// CREATE PROCEDURE get_all_users()
// BEGIN
//     SELECT * FROM users;
// END$$

// DELIMITER ;

// Run:

// CALL get_all_users();

// Procedure with Parameters

// Aap procedure ko input parameters de sakte ho.

// Example:

// DELIMITER $$

// CREATE PROCEDURE get_user_by_id(IN userId INT)
// BEGIN
//     SELECT * FROM users WHERE id = userId;
// END$$

// DELIMITER ;


// Run:

// CALL get_user_by_id(5);

// Procedure with Output Parameter

// Procedure se value wapis bhi aa sakti hai.

// DELIMITER $$

// CREATE PROCEDURE count_users(OUT total INT)
// BEGIN
//     SELECT COUNT(*) INTO total FROM users;
// END$$

// DELIMITER ;


// Run:

// CALL count_users(@u);
// SELECT @u;

// Procedure with INSERT
// DELIMITER $$

// CREATE PROCEDURE add_user(IN name VARCHAR(100), IN email VARCHAR(100))
// BEGIN
//     INSERT INTO users (name, email) 
//     VALUES (name, email);
// END$$

// DELIMITER ;


// Run:

// CALL add_user('Ali', 'ali@gmail.com');

// Procedure with Update
// DELIMITER $$

// CREATE PROCEDURE update_email(IN userId INT, IN newEmail VARCHAR(100))
// BEGIN
//     UPDATE users SET email = newEmail WHERE id = userId;
// END$$

// DELIMITER ;

// Procedure with IF Conditions
// DELIMITER $$

// CREATE PROCEDURE check_age(IN age INT)
// BEGIN
//     IF age >= 18 THEN
//         SELECT 'Adult';
//     ELSE
//         SELECT 'Minor';
//     END IF;
// END$$

// DELIMITER ;

// Procedure with Multiple Queries
// DELIMITER $$

// CREATE PROCEDURE register_user(
//     IN name VARCHAR(100),
//     IN email VARCHAR(100)
// )
// BEGIN
//     INSERT INTO users (name, email) VALUES (name, email);

//     SELECT LAST_INSERT_ID() AS new_user_id;

//     SELECT * FROM users;
// END$$

// DELIMITER ;

// Procedure ko Delete (Drop) kaise karte hain?
// DROP PROCEDURE IF EXISTS get_all_users;


// Important Concepts
// | Concept       | Meaning                               |
// | ------------- | ------------------------------------- |
// | **IN**        | Input Parameter                       |
// | **OUT**       | Output Parameter                      |
// | **INOUT**     | Input + Output                        |
// | **BEGIN…END** | Procedure ka body                     |
// | **DELIMITER** | SQL ke code block ko define karta hai |
// | **CALL**      | Procedure ko run karne ka command     |


// Simple Real-Life Example

// Agar aap ko rozana ye queries chalani hoti hain:

// SELECT COUNT(*) FROM users WHERE status='active';
// SELECT * FROM users WHERE status='active';


// To instead of writing it again & again →
// aik procedure banao:

// CREATE PROCEDURE active_users()
// BEGIN
//     SELECT COUNT(*) AS total_active FROM users WHERE status='active';
//     SELECT * FROM users WHERE status='active';
// END;


// Ab sirf:

// CALL active_users();


// PHP (Laravel, Raw PHP)

// Yes — sabse zyada use hoti hai.

// Example (Laravel):

// DB::select("CALL get_users()");

//  JavaScript / Node.js

// Example:

// connection.query("CALL get_users()", function(err, result){});

//  Python

// Example:

// cursor.callproc('get_users')

//  Java

// Example:

// CallableStatement stmt = conn.prepareCall("{CALL get_users()}");

//  C# / .NET

// Example:

// cmd.CommandType = CommandType.StoredProcedure;
// cmd.CommandText = "get_users";

//  C++

// Database connector use karke.

//  Go (Golang)

// Example:

// db.Query("CALL get_users()")

//  Ruby

// Example:

// client.query("CALL get_users()")