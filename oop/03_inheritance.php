<?php

// ============================================================================
// 03 — INHERITANCE (2nd Pillar of OOP)
// ============================================================================

// Inheritance ka matlab hai: Ek Class (Child) doosri Class (Parent) ki
// properties aur methods ko "inherit" (virsa mein le) kar sakti hai,
// bina dobara likhe.

// Real Life Example:
// Employee → har employee ka naam, salary hota hai, salary calculate hoti hai
// Manager  → Employee hi hai, LEKIN extra: team members, bonus bhi hota hai
// Manager Employee ki saari cheezen "inherit" karega + apni nayi cheezen add karega


// ============================================================================
// BASIC INHERITANCE
// ============================================================================

class Employee
{
    public function __construct(
        protected string $name,
        protected float $baseSalary
    ) {}

    public function calculateSalary(): float
    {
        return $this->baseSalary;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

// "extends" keyword se Manager, Employee ko inherit karta hai
class Manager extends Employee
{
    private array $teamMembers = [];

    public function __construct(
        string $name,
        float $baseSalary,
        private float $bonus = 0
    ) {
        // parent ka constructor call karna ZAROORI hai agar parent
        // constructor mein kuch properties set ho rahi hon
        parent::__construct($name, $baseSalary);
    }

    public function addTeamMember(string $employeeName): void
    {
        $this->teamMembers[] = $employeeName;
    }

    // METHOD OVERRIDING — Parent ka method REPLACE kar diya
    public function calculateSalary(): float
    {
        // parent:: se parent ka original method bhi call kar sakte hain
        $base = parent::calculateSalary();

        return $base + $this->bonus;
    }

    public function getTeamSize(): int
    {
        return count($this->teamMembers);
    }
}


// ============================================================================
// USAGE
// ============================================================================

$employee = new Employee('Ali', 50000);
$manager  = new Manager('Waqas', 80000, 15000);

$manager->addTeamMember('Ali');
$manager->addTeamMember('Sara');

echo $employee->calculateSalary();  // 50000
echo $manager->calculateSalary();   // 95000 (80000 + 15000 bonus)
echo $manager->getName();           // "Waqas" — Employee se inherit hua method
echo $manager->getTeamSize();       // 2


// ============================================================================
// protected KEYWORD KA ROLE
// ============================================================================

// $name aur $baseSalary "protected" hain (private nahi) — isliye Manager
// (child class) inko access kar sakti hai, lekin bahar se koi nahi.

// Agar private hote, to Manager bhi inko directly access nahi kar pati
// (sirf parent ke public/protected methods ke through access milta).


// ============================================================================
// final KEYWORD
// ============================================================================

// final class ka matlab: is class ko koi aur EXTEND nahi kar sakta
// final method ka matlab: child class ye method OVERRIDE nahi kar sakti

final class PaymentReceipt
{
    // Ye class ab kabhi extend nahi ho sakti — security/integrity ke liye
    // jaise payment receipts ka format kabhi tamper na ho.
}

class Vehicle
{
    final public function getVin(): string
    {
        // VIN generate karne ka logic kabhi child class override nahi kar sakti
        return 'VIN-12345';
    }
}


// ============================================================================
// MULTI-LEVEL INHERITANCE
// ============================================================================

class Animal
{
    public function eat(): string { return "Eating..."; }
}

class Mammal extends Animal
{
    public function breathe(): string { return "Breathing air..."; }
}

class Dog extends Mammal
{
    public function bark(): string { return "Woof!"; }
}

$dog = new Dog();
echo $dog->eat();      // Animal se aaya
echo $dog->breathe();  // Mammal se aaya
echo $dog->bark();     // Khud ka

// PHP mein MULTIPLE inheritance NAHI hoti (ek class do classes ko extend
// nahi kar sakti). Iska solution Interfaces aur Traits hain (07, 06 files dekho).


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. "Composition over Inheritance" — Senior developers gehri inheritance
//    chains (Animal -> Mammal -> Dog -> Puppy -> ...) avoid karte hain.
//    Agar relation "IS-A" nahi balke "HAS-A" hai, to Composition use karo:
//
//    class Car {
//        public function __construct(private Engine $engine) {}  // HAS-A Engine
//    }
//    (Inheritance: Car extends Vehicle → IS-A Vehicle ✅ sahi)
//    (Composition: Car has Engine → Car IS NOT Engine ❌ inheritance galat hoga)

// 2. Inheritance TIGHT COUPLING create karta hai — Child class, Parent
//    class ke internal implementation par depend karne lagti hai.
//    Isliye interfaces/abstraction zyada flexible hote hain (05, 06 files).

// 3. parent::__construct() call karna mat bhoolo — agar nahi karoge to
//    parent ki properties initialize hi nahi hongi (PHP error nahi dega
//    lekin bugs aayenge jab tum parent ke properties access karoge).

// 4. Real-world rule of thumb: "Is this Manager REALLY an Employee?"
//    Agar answer YES hai (LSP follow hota hai — Liskov Substitution),
//    tabhi inheritance use karo. SOLID principles dekho: solid/solid.php
