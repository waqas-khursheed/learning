<?php

// ============================================================================
// 08 — STATIC PROPERTIES/METHODS + LATE STATIC BINDING (self vs static)
// ============================================================================

// "static" ka matlab hai: property/method OBJECT se nahi, CLASS se belong
// karta hai. Object banaye bina bhi access ho sakta hai.

// Real Life Example:
// Har Student (object) ka apna roll number hota hai (instance property).
// Lekin "Total Students" ek SHARED counter hai jo SAARE students ke
// beech COMMON hai — ye static property hogi.


// ============================================================================
// STATIC PROPERTIES
// ============================================================================

class Student
{
    private static int $totalStudents = 0;   // CLASS level — sab objects mein shared

    public function __construct(private string $name)
    {
        self::$totalStudents++;   // self:: = current class ko refer karta hai
    }

    public static function getTotalStudents(): int
    {
        return self::$totalStudents;
    }
}

new Student('Ali');
new Student('Sara');
new Student('Bilal');

echo Student::getTotalStudents();  // 3 — object banaye bina class:: se access


// ============================================================================
// STATIC METHODS — Utility/Helper functions ke liye common
// ============================================================================

class MathHelper
{
    public static function square(int $n): int
    {
        return $n * $n;
    }
}

echo MathHelper::square(5);  // 25 — object banane ki zaroorat nahi


// ============================================================================
// self:: vs static:: — SABSE CONFUSING LEKIN IMPORTANT INTERVIEW TOPIC
// ============================================================================

// self::  → Hamesha "is class" ko refer karta hai JAHAN method LIKHA gaya hai
// static:: → "Late Static Binding" — Us class ko refer karta hai jise
//            ACTUALLY CALL kiya gaya hai (runtime par decide hota hai)

class ParentModel
{
    public static function create(): self
    {
        // self hamesha ParentModel hi return karega, chahe child se call ho
        return new self();
    }

    public static function createStatic(): static
    {
        // static us class ko return karega jisse ACTUAL call hua
        return new static();
    }
}

class ChildModel extends ParentModel
{
}

$a = ChildModel::create();        // self::   → ParentModel object banega ❌ (galat expectation)
$b = ChildModel::createStatic();  // static:: → ChildModel object banega ✅ (sahi)

echo get_class($a);  // ParentModel
echo get_class($b);  // ChildModel


// ============================================================================
// REAL-WORLD EXAMPLE — SINGLETON PATTERN (static ka classic use case)
// ============================================================================

// Singleton = Class ka SIRF EK object pure application mein exist kare.
// Real use: Database Connection, Logger, Config Manager

class DatabaseConnection
{
    private static ?DatabaseConnection $instance = null;

    // private constructor — bahar se "new DatabaseConnection()" NAHI ho sakta
    private function __construct()
    {
        echo "Database connection bana (sirf EK baar hoga)";
    }

    // Ye hi sirf tareeqa hai instance lene ka
    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function query(string $sql): string
    {
        return "Query run hui: {$sql}";
    }
}

$db1 = DatabaseConnection::getInstance();  // "Database connection bana" print hoga
$db2 = DatabaseConnection::getInstance();  // Kuch print NAHI hoga (same instance reuse)

var_dump($db1 === $db2);  // true — DONO EK HI object hain


// ============================================================================
// STATIC IN LARAVEL — REAL EXAMPLE (Eloquent Model)
// ============================================================================

// Laravel ke Eloquent Models mein "static" pattern bohot common hai:
//
// $user = User::find(1);        // static method — object banaye bina call
// $users = User::where('age', '>', 18)->get();
//
// Andar se ye "static::query()" use karta hai (self:: nahi) taake
// jab tum "Admin extends User" karo, to Admin::find() bhi sahi
// "Admin" model return kare, na ke User (Late Static Binding ki wajah se)


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Static state TESTING mushkil karta hai — static properties
//    GLOBAL state ki tarah behave karti hain, isliye Unit Tests mein
//    isolate karna mushkil hota hai. Senior devs Dependency Injection
//    ko static state se zyada prefer karte hain.

// 2. Static methods MOCK/OVERRIDE nahi ho sakte traditional tareeqe se
//    (kyunke wo object instance se bound nahi hote) — testing ke liye
//    PHPUnit mein special tools (mockery static mocking) chahiye hote hain.

// 3. Rule of Thumb: Agar method ko OBJECT KE STATE (properties) ki
//    zaroorat nahi hai, to wo static ho sakta hai (jaise MathHelper::square()).
//    Agar object ke specific data par depend karta hai, to NON-static hona chahiye.

// 4. static:: hamesha use karo jab Inheritance ke sath Factory/Singleton
//    pattern bana rahe ho — self:: bugs create karega jab child class
//    extend karegi (jaisa upar ParentModel/ChildModel example mein dikha).
