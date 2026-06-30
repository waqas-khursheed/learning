<?php

// ============================================================================
// 01 — CLASS & OBJECT BASICS
// ============================================================================

// OOP (Object-Oriented Programming) ek programming approach hai jisme hum
// real-world cheezon ko "Objects" ke roop mein represent karte hain.

// CLASS  = Blueprint / Naqsha (jaise Car ka design/drawing)
// OBJECT = Us blueprint se bana hua actual instance (jaise tumhari real Car)

// Ek Class se MULTIPLE objects ban sakte hain, har object apna alag data rakhta hai.


// ============================================================================
// REAL EXAMPLE — CAR
// ============================================================================

class Car
{
    // PROPERTIES (data jo object ke pass hota hai)
    public string $brand;
    public string $model;
    public int $year;
    private int $speed = 0;   // private = sirf isi class ke andar access ho sakta hai

    // CONSTRUCTOR — jab bhi "new Car(...)" likha jata hai, ye automatically chalta hai
    public function __construct(string $brand, string $model, int $year)
    {
        $this->brand = $brand;   // $this = current object ka reference
        $this->model = $model;
        $this->year  = $year;
    }

    // METHODS (actions jo object kar sakta hai)
    public function accelerate(int $amount): void
    {
        $this->speed += $amount;
    }

    public function getSpeed(): int
    {
        return $this->speed;
    }

    public function info(): string
    {
        return "{$this->year} {$this->brand} {$this->model} — Speed: {$this->speed} km/h";
    }

    // DESTRUCTOR — object destroy hote waqt (script khatam ya unset() hone par) chalta hai
    // Real use: file handle close karna, DB connection band karna, logging karna
    public function __destruct()
    {
        // echo "Car object destroy ho gaya: {$this->brand}";
    }
}


// ============================================================================
// OBJECT BANANA (INSTANTIATION)
// ============================================================================

$car1 = new Car('Toyota', 'Corolla', 2023);
$car2 = new Car('Honda', 'Civic', 2024);

$car1->accelerate(40);
$car2->accelerate(60);

echo $car1->info();  // 2023 Toyota Corolla — Speed: 40 km/h
echo $car2->info();  // 2024 Honda Civic — Speed: 60 km/h

// Dono objects ALAG hain — apna apna data, apna apna state rakhte hain.
// Ye OOP ka sabse bunyadi (fundamental) concept hai.


// ============================================================================
// $this KEYWORD
// ============================================================================

// $this hamesha "current object" ko refer karta hai — jis object par
// method call hua hai, uske andar $this usi object ko point karta hai.

// $car1->accelerate(40) call hone par, accelerate() ke andar $this == $car1
// $car2->accelerate(60) call hone par, accelerate() ke andar $this == $car2


// ============================================================================
// PROPERTY/METHOD VISIBILITY — QUICK INTRO (detail 02_encapsulation.php mein)
// ============================================================================

// public    → kahin se bhi access ho sakta hai (class ke bahar bhi)
// private   → SIRF isi class ke andar access ho sakta hai
// protected → isi class + child classes ke andar access ho sakta hai


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Constructor mein heavy logic (DB call, API call) MAT likho —
//    constructor sirf object ko "initialize" karne ke liye hai.

// 2. PHP mein object hamesha "by reference" (reference jaisa) pass hota hai
//    jab variable mein assign ya function mein pass karte ho:
//
//    $car3 = $car1;       // $car3 aur $car1 SAME object ko point karte hain
//    $car3->accelerate(10);
//    echo $car1->getSpeed(); // 50 — kyunke dono ek hi object hain!
//
//    Agar ALAG copy chahiye to clone keyword use karo: $car3 = clone $car1;
//    (Cloning detail 11_advanced_oop_concepts.php mein)

// 3. Type declarations (string, int, void) PHP 7+ mein lagana best practice hai —
//    isse bugs compile-time jaisa pehle hi pakray jate hain.

// 4. strict_types lagana senior projects mein standard hai:
//    declare(strict_types=1);  (file ke sabse upar, <?php ke turant baad)
//    Isse PHP "loose type juggling" nahi karega (e.g. "5" string ko 5 int nahi banayega)
