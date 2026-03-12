<?php 

//  SOLID Principles – Definition
// SOLID object-oriented programming ke 5 design rules ka set hai jo code ko clean, maintainable, reusable, aur scalable banate hain.

// Kya hotay hain SOLID principles?
// SOLID ka matlab hai:
// S – Single Responsibility Principle:
// Har class ka sirf ek hi kaam (responsibility) hona chahiye.

// O – Open/Closed Principle:
// Code extend kiya ja sakta hai lekin modify nahi karna chahiye.

// L – Liskov Substitution Principle:
// Child class parent class ki jagah use ho sakti hai bina system tode.

// I – Interface Segregation Principle:
// Chhoti, focused interfaces banao; clients ko unnecessary methods implement na karna paday.

// D – Dependency Inversion Principle:
// Code abstractions (interfaces) par depend kare, concrete implementations par nahi.

//  Kyun use karte hain?

// Code ko samajhna aur maintain karna easy hota hai

// Reusability badhti hai

// Testing aur debugging asaan hoti hai

// System scalable aur flexible ban jata hai

// Team projects mein conflicts aur errors kam hotay hain

//  Kya ye zaroori hain?

// Haan, bohot zaroori hain.
// Ye principles ensure karte hain ke aapka code:

// Future changes ke liye ready ho

// Large projects mein stable rahe

// Bugs aur coupling se free ho

// In short:
//  SOLID = Professional, future-proof software design. -->



// S – Single Responsibility Principle (SRP)
//  Definition:

// “A class should have only one reason to change.”
// Matlab: Har class ka sirf ek hi kaam (responsibility) hona chahiye.

//  Kyun use karte hain:

// Code clean aur understandable ban jata hai.

// Agar ek kaam badalta hai to doosra kaam affect nahi hota.

// Testing aur maintenance easy ho jati hai.

//  Bad Example (Violation of SRP):

class OrderService {
    public function createOrder($data) {
        // Order create kar raha hai
    }

    public function sendOrderEmail($order) {
        // Email bhi bhej raha hai (extra responsibility)
    }
}

// Yahan OrderService do kaam kar raha hai:
// Order create karna
// Email bhejna
// Agar email ka process badla, to order ka code bhi modify hoga — violation of SRP.

// Good Example (Follows SRP):

class OrderService {
    public function createOrder($data) {
        // Sirf order create kare
    }
}

class OrderMailer {
    public function sendOrderEmail($order) {
        // Sirf email bheje
    }
}


// Ab dono classes ka ek specific kaam hai.
// Code reusable, maintainable aur testable ho gaya




// O – Open/Closed Principle (OCP)

// Definition:
// “Software entities (classes, modules, functions) should be open for extension, but closed for modification.”

// Matlab:
// Aap apne code mein naye features add kar sako (extend kar sako) bina purana code badle (modify kiye).

//  Kyun use karte hain:

// Code stable aur safe rehta hai (old code toot’tā nahi).

// Naye features add karna easy hota hai.

// Future changes ke liye system ready rehta hai.

//  Bad Example (Violation of OCP):


class PaymentProcessor {
    public function pay($type) {
        if ($type === 'paypal') {
            // PayPal payment logic
        } elseif ($type === 'stripe') {
            // Stripe payment logic
        }
    }
}

// Problem:
// Agar kal “RazorPay” add karni ho to ye class modify karni padegi — OCP break ho gaya 


// Good Example (Follows OCP):


interface PaymentMethod {
    public function pay();
}

class PayPalPayment implements PaymentMethod {
    public function pay() {
        // PayPal payment logic
    }
}

class StripePayment implements PaymentMethod {
    public function pay() {
        // Stripe payment logic
    }
}

class PaymentProcessor {
    public function process(PaymentMethod $method) {
        $method->pay();
    }
}


// Ab agar naya gateway (e.g. RazorPay) add karna ho to sirf nayi class likhni hai:

class RazorPayPayment implements PaymentMethod {
    public function pay() {
        // RazorPay logic
    }
}


// Purana code untouched — system open for extension, closed for modification.


// L – Liskov Substitution Principle (LSP)
//  Definition:

// “Objects of a child (subclass) should be replaceable for their parent (superclass) without breaking the system.”

// Matlab:
// Agar ek class doosri class se inherit karti hai, to wo parent ki jagah use ho sake bina code tod ke.

//  Kyun use karte hain:

// Code predictable aur reliable rehta hai.

// Inheritance sahi tarike se kaam karti hai.

// Bugs aur unexpected behavior kam hotay hain.

// ❌ Bad Example (Violation of LSP):

class Bird {
    public function fly() {
        return "Flying in the sky";
    }
}

class Penguin extends Bird {
    public function fly() {
        throw new Exception("Penguins can't fly!");
    }
}

// Problem:
// Penguin technically Bird hai, lekin wo fly nahi kar sakta, to jab hum Penguin ko Bird ki jagah use karte hain, system break ho jata hai.
//  LSP violated ❌


// Good Example (Follows LSP):

abstract class Bird {
    abstract public function move();
}

class Sparrow extends Bird {
    public function move() {
        return "Flying in the sky";
    }
}

class Penguin extends Bird {
    public function move() {
        return "Swimming in the water";
    }
}

// Ab dono subclasses (Sparrow, Penguin) apni movement define karte hain.
// System ab safe aur consistent hai

// Simple Words Mein:

// Child class parent ke behavior ko todni nahi chahiye,
// balki use karne walay code ke liye same tarah se kaam karni chahiye.


// I – Interface Segregation Principle (ISP)
//  Definition:

// “No client should be forced to depend on methods it does not use.”

// Matlab:
// Interfaces ko chhota aur specific rakho —
// har class ko sirf wahi methods implement karne chahiye jo uske kaam ke hain.

//  Kyun use karte hain:

// Code clean aur flexible rehta hai

// Unnecessary methods implement karne ki majboori nahi hoti

// Classes focused aur reusable banti hain

// Bad Example (Violation of ISP):

interface Worker {
    public function work();
    public function eat();
}

class Robot implements Worker {
    public function work() {
        // Robot works
    }

    public function eat() {
        // ❌ Robots don't eat!
        throw new Exception("Robots don't eat!");
    }
}

// Problem:
// Robot ko eat() method implement karna force kiya gaya, jabke wo uske kaam ka nahi.
// Ye ISP violate karta hai.

// Good Example (Follows ISP):

interface Workable {
    public function work();
}

interface Eatable {
    public function eat();
}

class Human implements Workable, Eatable {
    public function work() {
        // Human works
    }

    public function eat() {
        // Human eats
    }
}

class Robot implements Workable {
    public function work() {
        // Robot works
    }
}

// Ab har class sirf wahi interface implement karti hai jo relevant hai 
// Koi bhi class unnecessary method implement nahi karti.

// Simple Words Mein:

// "Ek hi bada interface sab pe mat thopo —
// specific chhoti interfaces banao taake har class sirf apna kaam kare."


// D – Dependency Inversion Principle (DIP)
//  Definition:

// “High-level modules should not depend on low-level modules.
// Both should depend on abstractions.”

// Aur:

// “Abstractions should not depend on details.
// Details should depend on abstractions.”

//  Simple Words Mein:

// Apna code direct concrete classes (details) pe depend na kare,
// balki interfaces ya abstractions pe depend kare.

// Matlab:
// Agar kal aap dependency change karo (e.g., MySQL se MongoDB),
// to high-level code ko change na karna pade.

// ❌ Bad Example (Violation of DIP):

class MySQLDatabase {
    public function connect() {
        // Connect to MySQL
    }
}

class UserService {
    private $db;

    public function __construct() {
        $this->db = new MySQLDatabase(); // ❌ Direct dependency
    }

    public function getUser() {
        $this->db->connect();
        // Get user
    }
}


// Problem:
// Agar kal aapko PostgreSQL ya MongoDB use karna ho,
// to aapko UserService class change karni padegi.
// Ye tight coupling hai

// Good Example (Follows DIP):

interface DatabaseConnection {
    public function connect();
}

class MySQLDatabase implements DatabaseConnection {
    public function connect() {
        // Connect to MySQL
    }
}

class PostgreSQLDatabase implements DatabaseConnection {
    public function connect() {
        // Connect to PostgreSQL
    }
}

class UserService {
    private $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db; // ✅ Depends on abstraction
    }

    public function getUser() {
        $this->db->connect();
        // Get user
    }
}


// Usage:

$service = new UserService(new MySQLDatabase());
$service->getUser();


// Ab agar aapko DB change karni ho:

$service = new UserService(new PostgreSQLDatabase());


// Aapko UserService class me koi change nahi karna padta 
// Yehi hai Dependency Inversion Principle.

// Short Summary of DIP:

// High-level code should depend on interfaces, not concrete classes.

// Ye code ko modular, testable, aur scalable banata hai.


// SOLID Principles Summary

// | No.   | Principle                           | Full Form                                                                 | Definition (Simple Words Mein)                       | Example Summary                                                                  |
// | ----- | ----------------------------------- | ------------------------------------------------------------------------- | ---------------------------------------------------- | -------------------------------------------------------------------------------- |
// | **S** | **Single Responsibility Principle** | A class should have **only one reason to change**                         | Har class sirf **ek kaam** kare                      | `InvoicePrinter` aur `InvoiceSaver` alag classes hon                             |
// | **O** | **Open/Closed Principle**           | Classes should be **open for extension**, but **closed for modification** | Naye features **add karo**, purana code **na badlo** | Naye discount rules add karne ke liye naye class banao, purani ko change na karo |
// | **L** | **Liskov Substitution Principle**   | Subclasses should be **replaceable** for their base class                 | Child class **base jesa hi behave** kare             | `Penguin` ko `Bird` ke jesa treat kar sakte ho bina error ke                     |
// | **I** | **Interface Segregation Principle** | Clients should not depend on **methods they don’t use**                   | **Chhoti specific interfaces** banao                 | `Worker` aur `Eater` alag interfaces hon, Robot sirf `Worker` use kare           |
// | **D** | **Dependency Inversion Principle**  | Depend on **abstractions**, not **concretions**                           | High-level code **interface** pe depend kare         | `UserService` → `DatabaseInterface`, na ke `MySQLDatabase`                       |


// Why SOLID is Important:
// Code becomes modular (easily changeable)

// Reusability and scalability increase

// Fewer bugs when you extend or refactor

// Professional-level clean architecture (senior-level interviews love this)