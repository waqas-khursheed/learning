<?php

// ============================================================================
// 05 — ABSTRACTION (4th Pillar of OOP)
// ============================================================================

// Abstraction ka matlab hai: COMPLEX implementation details CHHUPANA,
// aur sirf ZAROORI/ESSENTIAL cheezen bahar dikhana (interface/contract).

// Real Life Example:
// Car chalate waqt tum sirf "steering, accelerator, brake" use karte ho.
// Engine ke andar combustion kaise hota hai, ye tumhe pata hone ki
// zaroorat nahi — wo COMPLEXITY abstract (chhupi) hai.


// ============================================================================
// ABSTRACT CLASS
// ============================================================================

// abstract class — Iska DIRECT object nahi ban sakta (new AbstractClass() ❌)
// Ye sirf EXTEND hone ke liye banti hai — child classes ko ek "template" deti hai.

abstract class Notification
{
    // Concrete (normal) method — child classes ko free mein milta hai
    public function log(string $message): void
    {
        echo "[LOG] Notification bheji ja rahi hai: {$message}";
    }

    // Abstract method — sirf SIGNATURE hai, BODY nahi.
    // Har child class ko ye method KHUD implement karna ZAROORI hai.
    abstract public function send(string $to, string $message): bool;
}

class EmailNotification extends Notification
{
    public function send(string $to, string $message): bool
    {
        $this->log($message);  // parent ka concrete method use kar liya
        // Real email-sending logic yahan hoga (Mail::to()->send() jaisa)
        echo "Email bheja gaya: {$to}";
        return true;
    }
}

class SmsNotification extends Notification
{
    public function send(string $to, string $message): bool
    {
        $this->log($message);
        echo "SMS bheja gaya: {$to}";
        return true;
    }
}

// $n = new Notification();  // ❌ FATAL ERROR — abstract class ka object nahi ban sakta
$email = new EmailNotification();
$email->send('ali@example.com', 'Aapka order confirm ho gaya');


// ============================================================================
// ABSTRACT CLASS vs INTERFACE — SABSE ZYADA PUCHA JANE WALA INTERVIEW QUESTION
// ============================================================================

// | Feature                       | Abstract Class                  | Interface                          |
// |--------------------------------|----------------------------------|--------------------------------------|
// | Methods with body              | ✅ Ho sakte hain (concrete)      | ❌ PHP 8 tak nahi (sirf signature)  |
// | Properties                     | ✅ Ho sakti hain                 | ❌ Nahi (sirf constants)            |
// | Multiple inheritance            | ❌ Ek hi class extend ho sakti  | ✅ Multiple interfaces implement    |
// | Constructor                    | ✅ Ho sakta hai                  | ❌ Nahi                             |
// | Access modifiers on methods    | ✅ public/protected              | Sirf public (implicitly)            |
// | Kab use karein                 | "IS-A" relation + shared code   | "CAN-DO" capability/contract        |


// ============================================================================
// KAB ABSTRACT CLASS, KAB INTERFACE? (Real Decision Making)
// ============================================================================

// ABSTRACT CLASS use karo jab:
// - Child classes mein KUCH common code/behavior SHARE karna ho (jaise log())
// - Classes ka aapas mein strong "IS-A" relationship ho (EmailNotification IS-A Notification)

// INTERFACE use karo jab:
// - Sirf ek CONTRACT define karna ho ("jo bhi ye implement karega, isme ye method hoga")
// - Unrelated classes mein SAME capability honi chahiye (jaise Countable — Array aur Collection dono)
// - Multiple "capabilities" ek class mein chahiye (PHP multiple interface implement allow karta hai)


// ============================================================================
// REAL-WORLD MIX EXAMPLE (Abstract Class + Interface dono saath)
// ============================================================================

interface Payable
{
    public function calculatePay(): float;
}

abstract class StaffMember implements Payable
{
    public function __construct(
        protected string $name,
        protected float $baseSalary
    ) {}

    // Common method — saare staff members ke liye same
    public function getName(): string
    {
        return $this->name;
    }

    // Har staff type ka pay calculation ALAG hoga — isliye abstract
    abstract public function calculatePay(): float;
}

class FullTimeStaff extends StaffMember
{
    public function calculatePay(): float
    {
        return $this->baseSalary;  // Fixed monthly salary
    }
}

class FreelanceStaff extends StaffMember
{
    public function __construct(
        string $name,
        private float $hourlyRate,
        private int $hoursWorked
    ) {
        parent::__construct($name, 0);
    }

    public function calculatePay(): float
    {
        return $this->hourlyRate * $this->hoursWorked;
    }
}

$staff = [
    new FullTimeStaff('Ali', 60000),
    new FreelanceStaff('Sara', 1500, 40),
];

foreach ($staff as $member) {
    echo "{$member->getName()}: PKR {$member->calculatePay()}";
}


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Abstraction ka asal goal "API simplicity" hai — caller ko sirf
//    pata hona chahiye "KYA" hota hai, "KAISE" hota hai uski fikar nahi.
//    Jaise Laravel ka Mail::send() — tumhe SMTP protocol ka pata hone
//    ki zaroorat nahi.

// 2. Common mistake: Abstraction ko Encapsulation samajh lena.
//    - Encapsulation = data ko HIDE/PROTECT karna (private/public)
//    - Abstraction   = complexity ko HIDE karke sirf essential interface dena

// 3. "Program to an interface, not an implementation" — ye SOLID ka
//    Dependency Inversion Principle hai. Dekho: solid/solid.php

// 4. PHP 8+ mein Interface mein constants ho sakte hain:
//    interface Payable { const TAX_RATE = 0.05; }
