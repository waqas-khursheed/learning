// ============================================================================
// 09 — PROTOTYPES & ES6 CLASSES
// ============================================================================


// ============================================================================
// PROTOTYPE KYA HAI? — JS ka "asal" OOP mechanism (Classes iske UPAR bani hain)
// ============================================================================

// Har JavaScript OBJECT ka EK "prototype" hota hai — jahan se wo
// properties/methods "udhar" (inherit) leta hai agar khud ke pass na hon.

function Animal(name) {
    this.name = name;
}

// Prototype par method add karna (HAR instance is method ko SHARE karega,
// memory mein ek hi copy banti hai — naya copy har object ke liye nahi):
Animal.prototype.eat = function () {
    return `${this.name} kha raha hai`;
};

const dog = new Animal("Dog");
const cat = new Animal("Cat");

console.log(dog.eat());   // "Dog kha raha hai"
console.log(cat.eat());   // "Cat kha raha hai"
console.log(dog.eat === cat.eat);   // true — DONO same function SHARE kar rahe hain


// ============================================================================
// ES6 CLASS — Prototype ka MODERN, CLEAN SYNTAX (ANDAR SE WAHI prototype hai)
// ============================================================================

class Car {
    // Constructor — jab "new Car(...)" likha jaye, ye chalta hai
    constructor(brand, model) {
        this.brand = brand;
        this.model = model;
        this.speed = 0;
    }

    // Class METHODS automatically PROTOTYPE par jate hain (shared hote hain)
    accelerate(amount) {
        this.speed += amount;
        return this.speed;
    }

    getInfo() {
        return `${this.brand} ${this.model} — Speed: ${this.speed}`;
    }
}

const myCar = new Car("Toyota", "Corolla");
myCar.accelerate(50);
console.log(myCar.getInfo());   // "Toyota Corolla — Speed: 50"

// (Poora OOP — Encapsulation, Inheritance, Polymorphism — concept-wise
// PHP ke `oop/` folder jaisa hi hai, sirf syntax JS ka hai)


// ============================================================================
// INHERITANCE — extends aur super()
// ============================================================================

class ElectricCar extends Car {
    constructor(brand, model, batteryCapacity) {
        super(brand, model);   // Parent (Car) ka constructor call karna ZAROORI hai
        this.batteryCapacity = batteryCapacity;
    }

    // Parent ka method OVERRIDE karna
    getInfo() {
        return `${super.getInfo()} | Battery: ${this.batteryCapacity}kWh`;
    }
}

const tesla = new ElectricCar("Tesla", "Model 3", 75);
tesla.accelerate(100);   // Parent ka method, INHERIT hua
console.log(tesla.getInfo());   // "Tesla Model 3 — Speed: 100 | Battery: 75kWh"


// ============================================================================
// GETTERS aur SETTERS — Property jaisi dikhti hain, lekin METHOD hoti hain
// ============================================================================

class BankAccount {
    #balance = 0;   // PRIVATE field (# prefix — modern JS, ES2022)

    constructor(initialBalance) {
        this.#balance = initialBalance;
    }

    // GETTER — property ki tarah READ hoti hai (bina () ke)
    get balance() {
        return this.#balance;
    }

    // SETTER — property ki tarah ASSIGN hoti hai (bina () ke)
    set balance(amount) {
        if (amount < 0) {
            throw new Error("Balance negative nahi ho sakta");
        }
        this.#balance = amount;
    }

    deposit(amount) {
        this.#balance += amount;
    }
}

const account = new BankAccount(1000);
console.log(account.balance);   // 1000 — getter call hua (bina () ke!)
account.balance = 5000;          // setter call hua
console.log(account.balance);   // 5000

// console.log(account.#balance);  // ❌ SYNTAX ERROR — private field bahar se accessible nahi


// ============================================================================
// STATIC METHODS/PROPERTIES — Class se directly, OBJECT banaye bina
// ============================================================================

class MathHelper {
    static PI = 3.14159;

    static square(n) {
        return n * n;
    }
}

console.log(MathHelper.PI);          // 3.14159 — object banane ki zaroorat nahi
console.log(MathHelper.square(5));   // 25

// REAL USE CASE — Factory pattern (dekho oop/12_design_patterns):
class User {
    constructor(name) {
        this.name = name;
    }

    static createGuest() {
        return new User("Guest");
    }
}

const guest = User.createGuest();
console.log(guest.name);   // "Guest"


// ============================================================================
// ABSTRACT-JAISA PATTERN — JS mein TRUE abstract class nahi hoti, lekin simulate kar sakte hain
// ============================================================================

class Shape {
    constructor() {
        if (this.constructor === Shape) {
            throw new Error("Shape class ko directly instantiate NAHI kar sakte");
        }
    }

    area() {
        throw new Error("area() method child class mein implement karna ZAROORI hai");
    }
}

class Circle extends Shape {
    constructor(radius) {
        super();
        this.radius = radius;
    }

    area() {
        return Math.PI * this.radius ** 2;
    }
}

// new Shape();   // ❌ ERROR — "Shape class ko directly instantiate NAHI kar sakte"
const circle = new Circle(5);
console.log(circle.area());   // 78.53...


// ============================================================================
// instanceof — Object KIS class se bana hai check karna
// ============================================================================

console.log(tesla instanceof ElectricCar);   // true
console.log(tesla instanceof Car);            // true — INHERITANCE ki wajah se
console.log(tesla instanceof Animal);          // false


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. JS Classes "Syntactic Sugar" hain — ANDAR SE wahi PROTOTYPE-based
//    inheritance hai. Interview mein ye samjhana IMPORTANT hai.

// 2. Private fields (#) ES2022 mein aaye — purani JS mein "_balance"
//    naming convention (underscore prefix) use hoti thi, jo SIRF
//    "convention" thi, ASAL mein private NAHI hoti thi.

// 3. extends ek hi class ko extend kar sakta hai (PHP ki tarah) —
//    multiple inheritance ke liye "Mixins" pattern use hota hai
//    (PHP Traits jaisa concept — dekho oop/07_traits.php)
