// OOP hota kya hai?

// Object-Oriented Programming ek programming style hai jisme hum real-world cheezon ko objects ke form me represent karte hain.
// Har object ke paas:

// Properties (data) hoti hain

// Methods (actions) hoti hain

// Example (real-world):

// Car → ek object hai
// Properties: color, brand, model
// Methods: start(), stop(), accelerate()

// 🔹 4 Pillars of OOP

// Har OOP language (PHP, JS, Java, Python...) in 4 basic pillars pe based hoti hai:
// | Concept           | Description                                         | PHP Example                                              | JavaScript Example                      |
// | ----------------- | --------------------------------------------------- | -------------------------------------------------------- | --------------------------------------- |
// | **Encapsulation** | Data + methods ko ek class ke andar band karna      | `class User { private $name; function getName() {...} }` | `class User { #name; getName() {...} }` |
// | **Abstraction**   | Sirf zaruri details dikhana, complex logic chhupana | Interfaces / abstract classes                            | Classes or modules                      |
// | **Inheritance**   | Parent class ke features child class me use karna   | `class Admin extends User {}`                            | `class Admin extends User {}`           |
// | **Polymorphism**  | Same function alag-alag behavior                    | `function makeSound(Animal $a)` → Dog/Cat                | Method overriding                       |


// PHP vs JavaScript OOP — Same or Different?
// | Concept                           | PHP                                  | JavaScript                                                                 |
// | --------------------------------- | ------------------------------------ | -------------------------------------------------------------------------- |
// | **Language Type**                 | Class-based OOP                      | Prototype-based OOP (modern JS me class syntax same jesa lagta hai)        |
// | **Class Support**                 | Always class-based (`class User {}`) | ECMAScript 2015 (ES6) me `class` introduce hua, pehle prototype system tha |
// | **Access Modifiers**              | `public`, `private`, `protected`     | JavaScript me officially `#` use hota hai for private, baki sab public     |
// | **Constructor**                   | `__construct()`                      | `constructor()`                                                            |
// | **Inheritance**                   | `extends` keyword                    | `extends` keyword (same syntax)                                            |
// | **Static Members**                | `public static function foo()`       | `static foo()`                                                             |
// | **Interfaces / Abstract Classes** | Supported                            | JS me direct nahi, lekin achieve kar sakte ho manually                     |




// PHP Example (Laravel Style)

class User {
    private $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function greet() {
        return "Hello, " . $this->name;
    }
}

class Admin extends User {
    public function greet() {
        return "Welcome Admin, " . parent::greet();
    }
}

$user = new User("Ali");
$admin = new Admin("Waqas");

echo $user->greet();   // Hello, Ali
echo $admin->greet();  // Welcome Admin, Hello,


// 🔹 JavaScript Example (Node.js Style)

class User {
    #name;
    constructor(name) {
        this.#name = name;
    }

    greet() {
        return `Hello, ${this.#name}`;
    }
}

class Admin extends User {
    greet() {
        return `Welcome Admin, ${super.greet()}`;
    }
}

const user = new User("Ali");
const admin = new Admin("Waqas");

console.log(user.greet());  // Hello, Ali
console.log(admin.greet()); // Welcome Admin, Hello,

// Best Way to Master OOP
// Core Concepts likh lo aur examples likho (Encapsulation, Inheritance, etc.)
// Laravel Models aur Controllers me OOP thinking dekho
// JS me same class-based examples banao
// Design Patterns (next level OOP) seekho:
// Singleton
// Factory
// Repository
// Strategy
// Observer (Laravel Events/Listeners)

